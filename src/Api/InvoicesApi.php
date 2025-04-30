<?php

namespace mindtwo\LaravelSevdesk\Api;

use Exception;
use Illuminate\Support\Collection;
use mindtwo\LaravelSevdesk\Contracts\IsSevdeskCustomer;
use mindtwo\LaravelSevdesk\DataTransferObjects\Invoice;
use mindtwo\LaravelSevdesk\DataTransferObjects\InvoicePosition;
use mindtwo\LaravelSevdesk\Enums\Invoice\BookingTypeEnum;
use mindtwo\LaravelSevdesk\Enums\Invoice\InvoiceSendTypeEnum;
use mindtwo\LaravelSevdesk\Exceptions\CouldNotFetchInvoicesForCustomerException;

class InvoicesApi extends BaseApiService
{
    public function __construct(
        protected ?string $apiToken = null,
        protected ?string $sevUser = null,
        protected ?string $checkAccount = null,
    ) {
        parent::__construct($apiToken);

        if (empty($this->sevUser)) {
            throw new Exception('sevdesk user is required');
        }
    }

    /**
     * Create an invoice in sevDesk
     *
     * @param  array<InvoicePosition>|Collection<InvoicePosition>  $items
     */
    public function createInvoice(array|Invoice $invoice, array|Collection $items, bool $takeDefaultAddress = true): Invoice
    {
        // Create invoice
        $invoice = $invoice instanceof Invoice
            ? $invoice
            : Invoice::from($invoice);

        $data = [
            'invoice' => $invoice->toArray(),
            'invoicePosSave' => collect($items)->toArray(),
            'takeDefaultAddress' => $takeDefaultAddress,
        ];

        $response = $this->client()->post('Invoice/Factory/saveInvoice', $data);

        if (! $response->successful()) {
            throw new Exception('Could not create invoice');
        }

        $objects = $response->json('objects');

        return Invoice::from($objects['invoice']);
    }

    /**
     * Get an invoice from sevDesk
     *
     * @param  int  $invoiceId  - the invoice id from sevDesk
     */
    public function getInvoice(int $invoiceId): ?Invoice
    {
        $response = $this->client()->get("Invoice/{$invoiceId}");

        if (! $response->successful()) {
            throw new Exception('Could not get invoice');
        }

        $objects = $response->json('objects');

        return isset($objects[0]) ? Invoice::from($objects[0]) : null;
    }

    /**
     * Get all invoices for a customer
     *
     * @param  IsSevdeskCustomer  $customer  - the model that implements the IsSevdeskCustomer interface
     * @return array<Invoice>
     */
    public function getInvoicesByCustomer(IsSevdeskCustomer $customer): array
    {
        $response = $this->client()->get('Invoice', [
            'contact[id]' => $customer->getSevdeskCustomerId(),
            'contact[objectName]' => 'Contact',
            'status' => [200, 300, 500, 750, 1000],
        ]);

        if (! $response->successful()) {
            throw new CouldNotFetchInvoicesForCustomerException('Could not get invoices for customer '.$customer->getSevdeskCustomerId());
        }

        $objects = $response->json('objects');

        return Invoice::collect($objects ?? [], 'array');
    }

    /**
     * Get all invoices from sevDesk
     */
    public function getInvoices(): array
    {
        $response = $this->client()->get('Invoice');

        $objects = $response->json('objects');

        return $objects ?? [];
    }

    /**
     * Mark invoice as paid
     *
     * @param  int  $invoiceId  - the invoice id from sevDesk
     * @param  int  $amount  - the amount to mark as paid
     * @param  BookingTypeEnum  $type  - the payment type (FULL_PAYMENT, ...)
     */
    public function markInvoiceAsPaid(int $invoiceId, int|float $amount, BookingTypeEnum $type = BookingTypeEnum::FULL_PAYMENT): array
    {

        if (! $this->checkAccount) {
            throw new Exception('Check account is required');
        }

        // The request body
        $data = [
            'amount' => $amount,
            'date' => date('Y-m-d'),
            'type' => $type,
            'checkAccount' => [
                'id' => $this->checkAccount,
                'objectName' => 'CheckAccount',
            ],
        ];

        $response = $this->client()->put("Invoice/{$invoiceId}/bookAmount", $data);

        if (! $response->successful()) {
            throw new Exception('Could not mark invoice as paid');
        }

        $objects = $response->json();

        return $objects;
    }

    /**
     * Mark invoice as sent in sevDesk
     * This transitions the invoice from draft to sent
     *
     * @param  int  $invoiceId  - the invoice id from sevDesk
     * @param  InvoiceSendTypeEnum  $sendType  - the send type (VM = email, ...)
     */
    public function markAsSent(int $invoiceId, InvoiceSendTypeEnum $sendType = InvoiceSendTypeEnum::MAIL): array
    {
        $response = $this->client()->put("Invoice/{$invoiceId}/sendBy", [
            'sendType' => $sendType,
            'sendDraft' => false,
        ]);

        if (! $response->successful()) {
            throw new Exception('Could not mark invoice as sent');
        }

        return $response->json('objects');
    }

    /**
     * Send invoice by email
     *
     * @param  int  $invoiceId  - the invoice id from sevDesk
     * @param  string  $email  - the email address to send the invoice to
     * @param  ?string  $subject  - the subject of the email
     * @param  ?string  $text  - the text of the email
     */
    public function sendInvoiceViaMail(int $invoiceId, string $email, ?string $subject = null, ?string $text = null): array
    {
        // Send email
        $subject = $subject ?? config('sevdesk-api.invoice_email.subject');
        $text = $text ?? config('sevdesk-api.invoice_email.text');

        $response = $this->client()->post("Invoice/{$invoiceId}/sendViaEmail", [
            'toEmail' => $email,
            'subject' => $subject,
            'text' => $text,
        ]);

        if (! $response->successful()) {
            throw new Exception('Could not send invoice via mail');
        }

        return $response->json('objects');
    }
}
