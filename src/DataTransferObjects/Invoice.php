<?php

namespace mindtwo\LaravelSevdesk\DataTransferObjects;

use mindtwo\LaravelSevdesk\Enums\Invoice\InvoiceStatusEnum;
use mindtwo\LaravelSevdesk\Enums\Invoice\InvoiceTypeEnum;
use mindtwo\LaravelSevdesk\Enums\Invoice\TaxRuleEnum;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithCastable;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class Invoice extends Data
{
    public int|Optional $id;

    public string $objectName = 'Invoice';

    public ?string $invoiceNumber;

    // required
    /**
     * The contact used in the invoice
     *
     * required
     */
    #[WithCastable(Contact::class, normalize: true)]
    public Contact $contact;

    /**
     * The user who acts as a contact person for the invoice
     *
     * required
     */
    #[WithCastable(ContactPerson::class, normalize: true)]
    public ContactPerson $contactPerson;

    /**
     * Creation date of the invoice
     */
    public string|Optional $create;

    /**
     * Last updated date of the invoice
     */
    public string|Optional $update;

    /**
     * Client to which object belongs. Will be filled automatically
     */
    public array|Optional $sevClient;

    /**
     * The date of the invoice
     *
     * required
     */
    public string $invoiceDate;

    public string|Optional $header;

    public string|Optional $headText;

    public string|Optional $footText;

    public int|Optional $timeToPay;

    /**
     * If you want to give a discount, define the percentage here.
     *       Otherwise provide zero as value
     *
     * required
     */
    public int $discount = 0;

    /**
     * Complete address of the recipient including name, street, city, zip and country.<br>\r\n     Line breaks can be used and will be displayed on the invoice pdf.
     */
    public string|Optional $address;

    /**
     * The country of the invoice address.
     *
     * required
     */
    #[WithCastable(StaticCountry::class, normalize: true)]
    public StaticCountry $addressCountry;

    public string|Optional $payDate;

    /**
     * User who created the voucher. Will be filled automatically.
     */
    public array|Optional $createUser;

    public string|Optional $deliveryDate;

    public int|Optional $deliveryDateUntil;

    /**
     * Please have a look in our\r\n     <a href='#tag/Invoice/Types-and-status-of-invoices'>Types and status of invoices</a>\r\n     to see what the different status codes mean
     */
    #[WithCast(EnumCast::class, InvoiceStatusEnum::class)]
    public InvoiceStatusEnum $status;

    public bool|Optional $smallSettlement;

    /**
     * This is not used anymore. Use the taxRate of the individual positions instead.
     *
     * required
     */
    public float|Optional $taxRate;

    /**
     * **Use this in sevdesk-Update 2.0 (replaces taxType / taxSet).**
     *
     * required
     */
    #[WithCast(EnumCast::class, TaxRuleEnum::class)]
    public TaxRuleEnum $taxRule;

    /**
     * A common tax text would be 'Umsatzsteuer 19%'
     *
     * required
     */
    public string $taxText;

    /**
     * **Use this in sevdesk-Update 1.0 (instead of taxRule).**
     *
     *         Tax type of the order.
     *
     *         There are four tax types:
     *
     *         1. default - Umsatzsteuer ausweisen
     *
     *         2. eu - Steuerfreie innergemeinschaftliche Lieferung (Europäische
     *         Union)
     *
     *         3. noteu - Steuerschuldnerschaft des Leistungsempfängers (außerhalb
     *         EU, z. B. Schweiz)
     *
     *         4. custom - Using custom tax set
     *
     *         5. ss - Not subject to VAT according to §19 1 UStG
     *
     *         Tax rates are heavily connected to the tax type used.
     */
    public string|Optional $taxType;

    /**
     *  **Use this in sevdesk-Update 1.0 (instead of taxRule).**
     *
     *      Tax set of the order. Needs to be added if you chose the tax type
     *      custom
     */
    public array|Optional $taxSet;

    public int|Optional $dunningLevel;

    public array|Optional $paymentMethod;

    public string|Optional $sendDate;

    /**
     * The type of invoice. Please have a look in our\r\n     <a href='#tag/Invoice/Types-and-status-of-invoices'>Types and status of invoices</a>\r\n     to see what the different types mean
     */
    #[WithCast(EnumCast::class, InvoiceTypeEnum::class)]
    public InvoiceTypeEnum $invoiceType;

    public string|Optional $accountIntervall;

    public int|Optional $accountNextInvoice;

    /**
     * Currency used in the order. Needs to be currency code according to ISO-4217
     */
    public string $currency = 'EUR';

    public float|Optional $sumNet;

    public float|Optional $sumTax;

    public float|Optional $sumGross;

    public float|Optional $sumDiscounts;

    public float|Optional $sumNetForeignCurrency;

    public float|Optional $sumTaxForeignCurrency;

    public float|Optional $sumGrossForeignCurrency;

    public float|Optional $sumDiscountsForeignCurrency;

    public float|Optional $sumNetAccounting;

    public float|Optional $sumTaxAccounting;

    public float|Optional $sumGrossAccounting;

    public float|Optional $paidAmount;

    public bool $showNet;

    public string|Optional $enshrined;

    public string|Optional $sendType;

    public array|Optional $origin;

    public string|Optional $customerInternalNote;

    /**
     * If true, the invoice will be created as e-invoice.
     *
     *       To create a valid e-invoice some extra data are required
     *       - sevClient
     *           - addressStreet
     *           - addressZip
     *           - addressCity
     *           - bankIban
     *           - bankBic
     *           - contactEmail
     *           - contactPhone
     *           - taxNumber
     *           - vatNumber
     *       - contact
     *           - buyerReference
     *           - email
     *       - invoice
     *           - paymentMethod
     *           - addressStreet
     *           - addressZip
     *           - addressCity
     *           - addressCountry
     *           - contact
     */
    public bool|Optional $propertyIsEInvoice;

    public bool $mapAll;

    /**
     * Create a new invoice
     *
     * @param  array<string,mixed>  $parameters
     */
    public static function default(array $parameters = []): self
    {
        if (! isset($parameters['contact'])) {
            throw new \InvalidArgumentException('Contact field of parameters is required');
        }

        // Get the contact from the parameters
        $contact = $parameters['contact'];
        unset($parameters['contact']);

        $defaults = [
            'contact' => $contact,
            'contactPerson' => config('sevdesk.sev_user'),
            'invoiceDate' => date('Y-m-d'),
            'status' => InvoiceStatusEnum::DRAFT,
            // TODO - defaults
            //     'header' => 'Rechnung',
            //     'headText' => 'Für die Teilnahme am Deutschen Stiftungstag 2025 des Bundesverbandes Deutscher Stiftungen vom 21. bis 22. Mai 2025 in Wiesbaden berechnen wir:',
            //     'footText' => 'Die Tagungsgebühr ist gemäß §4 Nr. 22a UStG von der Umsatzsteuer befreit.<br><br> Bitte überweisen Sie den oben ausgewiesenen Rechnungsbetrag innerhalb von 14 Tagen <strong>unter Angabe des Verwendungszwecks (Name, Vorname + Rechnungsnummer)</strong> auf das nachfolgende Konto.<br><br> Ihr Ticket wird erst nach vollständigem Zahlungseingang gültig. Der Teilnehmendenausweis (Namensbadge) wird Ihnen 14 Tage vor Veranstaltungsbeginn postalisch zugesendet.<br><br>',
            'timeToPay' => config('services.sevdesk.invoice.time_to_pay'),
            'taxRule' => TaxRuleEnum::VAT_SUBJECTED,
            'addressCountry' => 1,
            'mapAll' => true,
            'invoiceNumber' => null,
            'invoiceType' => InvoiceTypeEnum::NORMAL,
            // TODO config default
            'currency' => 'EUR',
        ];

        return self::from([
            ...$defaults,
            ...$parameters,
        ]);

        // $parameters = [

        //     'paymentMethod' => [
        //         'id' => 21919,
        //         'objectName' => 'PaymentMethod',
        //     ],
        //     ...$data->billingAddress->toSevDeskInvoiceAddress(),
        // ];
    }
}
