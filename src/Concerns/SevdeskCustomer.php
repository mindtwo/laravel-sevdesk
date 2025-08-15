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
     * Update the sevdesk customer ID column.
     */
    public function updateSevdeskCustomerId(?int $sevdeskCustomerId): void
    {
        $columnName = $this->getSevdeskCustomerIdColumn();

        $this->{$columnName} = $sevdeskCustomerId;
        $this->save();
    }

    /**
     * METHODS TO HANDLE THE SEVDESK CUSTOMER
     */

    /**
     * Get or create the sevdesk customer.
     *
     * @return array - the SevDesk customer data
     */
    public function getOrCreateSevdeskCustomer(): array
    {
        // If the customer already exists, return it
        if ($this->getSevdeskCustomerId() !== null && ($data = $this->getSevdeskCustomer()) !== null) {
            return $data;
        }

        // Create the customer
        $this->createSevdeskCustomer();

        // Return the customer
        return $this->getSevdeskCustomer();
    }

    /**
     * Create or get the sevdesk customer.
     *
     * @param  Address|null  $address  - The address to create the customer with
     * @param  ContactTypeEnum|null  $contactType  - The contact type (e.g. customer, supplier)
     */
    public function createSevdeskCustomerWithAddress(?Address $address = null, ?ContactTypeEnum $contactType = null): self
    {
        // If the customer already exists, return it
        if ($this->getSevdeskCustomerId() !== null) {
            return $this;
        }

        $contactType = $contactType ?? ContactTypeEnum::CUSTOMER;

        // TODO - DTO
        // Customer data for SevDesk
        $customerData = [
            'academicTitle' => $this->title ?? '',
            'surename' => $this->first_name,
            'familyname' => $this->last_name,
            'category' => $contactType->toRequestArray(),
            'status' => ContactStatusEnum::Active->value,
            'buyerReference' => $this->uuid,
        ];

        // Send POST request to SevDesk API
        $contact = LaravelSevdesk::contacts()->createContact($customerData);

        if (! isset($contact['id'])) {
            throw new \Exception('Could not create SevDesk contact');
        }

        $this->updateSevdeskCustomerId($contact['id']);

        // If with communication ways is true, add communication ways to the customer
        if ($address !== null) {
            $this->addCommunicationWays($contact['id'], $address);
        }

        return $this;
    }

    /**
     * Create a customer in SevDesk.
     * Shorthand for createSevdeskCustomerWithAddress() with default parameters.
     */
    public function createSevdeskCustomer(): self
    {
        return $this->createSevdeskCustomerWithAddress();
    }

    /**
     * Create a customer in SevDesk.
     * Shorthand for createSevdeskCustomerWithAddress() with a specific contact type.
     *
     * @param  ContactTypeEnum  $contactType  - The contact type (e.g. customer, supplier)
     */
    public function createSevdeskCustomerWithType(ContactTypeEnum $contactType): self
    {
        return $this->createSevdeskCustomerWithAddress(null, $contactType);
    }

    /**
     * Create an invoice for the customer.
     *
     * @param  array  $invoicePositions  - The invoice positions
     * @param  array  $options  - The invoice options
     */
    public function createSevdeskInvoice(array $invoicePositions, array $options = []): Invoice
    {
        // If the customer ID is null, we try to create the customer
        if (! $this->getSevdeskCustomerId() === null) {
            $this->createSevdeskCustomer();
        }

        $customerId = $this->getSevdeskCustomerId();

        // If the customer ID is null, we cannot create an invoice
        if ($customerId === null) {
            throw new \Exception('Could not create SevDesk invoice, customer ID is null. Please create the customer first.');
        }

        $addressName = $options['addressName'] ?? $this->hasAttribute('name') ? $this->name : '';

        $invoiceData = Invoice::default([
            'contact' => $customerId,
            'addressName' => $addressName,
            ...$options,
        ]);

        $invoice = LaravelSevdesk::invoices()->createInvoice($invoiceData->toArray(), $invoicePositions);

        return $invoice;
    }

    /**
     * Check if the saved customer id is a valid SevDesk customer.
     *
     * @param  bool  $setCustomerIdToNullOnFail  - Whether to set the customer ID to null if the customer is not valid
     * @return array|null - The SevDesk customer data
     */
    public function validateSevdeskCustomer(bool $setCustomerIdToNullOnFail = true): bool
    {
        $valid = $this->getSevdeskCustomer() !== null;

        // If the customer is not valid, we set the customer ID to null
        if (! $valid && $setCustomerIdToNullOnFail) {
            $this->updateSevdeskCustomerId(null);
        }

        return $valid;
    }

    /**
     * Add communication ways to the customer.
     *
     * @param  int|null  $sevdeskCustomerId  - The SevDesk customer ID
     * @param  Address|null  $address  - The address to add
     */
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

    /**
     * Get the SevDesk customer data.
     *
     * @return array|null - The SevDesk customer data
     */
    protected function getSevdeskCustomer(): ?array
    {
        $customerId = $this->getSevdeskCustomerId();

        if ($customerId === null) {
            return null;
        }

        // Try to get the customer from SevDesk
        try {
            return LaravelSevdesk::contacts()->getContact($customerId);
        } catch (\Throwable $th) {
            Log::error('Could not get SevDesk customer: '.$customerId, [
                'exception' => $th,
            ]);
        }

        return null;
    }
}
