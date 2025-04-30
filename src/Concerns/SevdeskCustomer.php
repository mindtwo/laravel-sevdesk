<?php

namespace mindtwo\LaravelSevdesk\Concerns;

use Illuminate\Support\Facades\Log;
use mindtwo\LaravelSevdesk\DataTransferObjects\Address;
use mindtwo\LaravelSevdesk\DataTransferObjects\Invoice;
use mindtwo\LaravelSevdesk\Enums\Contact\ContactStatusEnum;
use mindtwo\LaravelSevdesk\Enums\Contact\ContactTypeEnum;
use mindtwo\LaravelSevdesk\Facades\LaravelSevdesk;

/**
 * Trait that is a basic implementation of the IsSevdeskCustomer interface.
 *
 * @phpstan-ignore trait.unused
 */
trait SevdeskCustomer
{
    /**
     * Get the sevdesk customer ID.
     */
    public function getSevdeskCustomerId(): ?int
    {
        return $this->attributes[$this->getSevdeskCustomerIdColumn()] ?? null;
    }

    /**
     * Get the column name for the sevdesk customer ID.
     */
    public function getSevdeskCustomerIdColumn(): string
    {
        if (property_exists($this, 'sevdeskCustomerIdColumn')) {
            return $this->sevdeskCustomerIdColumn;
        }

        return 'sevdesk_customer_id';
    }

    /**
     * METHODS TO HANDLE THE SEVDESK CUSTOMER
     */

    /**
     * Create or get the sevdesk customer.
     *
     * @param  Address|null  $address  - The address to create the customer with
     * @param  bool  $withCommunicationWays  - Whether to add communication ways to the customer
     */
    public function createOrGetSevdeskCustomer(?Address $address = null, bool $withCommunicationWays = true): self
    {
        if ($this->getSevdeskCustomerId() === null && $address === null && $withCommunicationWays) {
            return $this;
        }

        // If the customer already exists, return it
        if ($this->getSevdeskCustomerId() !== null) {
            return $this;
        }

        // Customer data for SevDesk
        $customerData = [
            'academicTitle' => $this->title ?? '',
            'surename' => $this->first_name,
            'familyname' => $this->last_name,
            'category' => ContactTypeEnum::CUSTOMER->toRequestArray(),
            'status' => ContactStatusEnum::Active->value,
            'buyerReference' => $this->uuid,
        ];

        // Send POST request to SevDesk API
        $contact = LaravelSevdesk::contacts()->createContact($customerData);

        if (! isset($contact['id'])) {
            throw new \Exception('Could not create SevDesk contact');
        }

        $columnName = $this->getSevdeskCustomerIdColumn();

        $this->{$columnName} = $contact['id'];
        $this->save();

        // If with communication ways is true, add communication ways to the customer
        if ($withCommunicationWays) {
            $this->addCommunicationWays($contact['id'], $address);
        }

        return $this;
    }

    /**
     * Create an invoice for the customer.
     *
     * @param  array  $invoicePositions  - The invoice positions
     * @param  array  $options  - The invoice options
     */
    public function createSevdeskInvoice(array $invoicePositions, array $options = []): Invoice
    {
        if ($this->getSevdeskCustomerId() === null) {
            $this->createOrGetSevdeskCustomer();
        }

        $invoiceData = Invoice::default([
            'contact' => $this->getSevdeskCustomerId(),
            ...$options,
        ]);

        $invoice = LaravelSevdesk::invoices()->createInvoice($invoiceData->toArray(), $invoicePositions);

        return $invoice;
    }

    protected function addCommunicationWays(?Address $address): void
    {
        $sevdeskCustomerId = $this->getSevdeskCustomerId();
        $email = $this->email;

        $callable = function () use ($sevdeskCustomerId, $address, $email) {
            if ($address !== null) {
                // Add address
                try {
                    LaravelSevdesk::contacts()->createContactAddress($sevdeskCustomerId, $address);
                } catch (\Throwable $th) {
                    Log::error('Could not create SevDesk address for user: '.$sevdeskCustomerId);
                }
            }

            // Add email communication way
            if ($this->email) {
                // Add as Communication Way
                try {
                    LaravelSevdesk::contacts()->createCommunicationWay($sevdeskCustomerId, $email);
                } catch (\Throwable $th) {
                    Log::error('Could not create SevDesk communication way for user: '.$sevdeskCustomerId);
                }
            }
        };

        if (app()->runningInConsole()) {
            $callable();

            return;
        }
        // Dispatch a job to create the communication way
        dispatch($callable)->afterResponse();
    }
}
