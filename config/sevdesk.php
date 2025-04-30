<?php

// config for mindtwo/LaravelSevdesk
return [

    /**
     * The token to access the sevdesk api
     *
     * @phpstan-ignore larastan.noEnvCallsOutsideOfConfig
     */
    'api_token' => env('SEVDESK_API_TOKEN'),

    /**
     * The sevdesk user id we act as.
     *
     * @phpstan-ignore larastan.noEnvCallsOutsideOfConfig
     */
    'sev_user' => env('SEVDESK_SEV_USER'),

    // TODO Check how we get this info
    /**
     * The sevdesk user id we act as.
     *
     * @phpstan-ignore larastan.noEnvCallsOutsideOfConfig
     */
    'check_account' => env('SEVDESK_CHECK_ACCOUNT'),

    /**
     * The defaults for the invoices
     */
    'invoice' => [
        /**
         * The default payment method for the invoice.
         *
         * @phpstan-ignore larastan.noEnvCallsOutsideOfConfig
         */
        'time_to_pay' => env('SEVDESK_TIME_TO_PAY', 14),

        /**
         * The default currency for the invoice.
         *
         * @phpstan-ignore larastan.noEnvCallsOutsideOfConfig
         */
        'currency' => env('SEVDESK_DEFAULT_CURRENCY', 'EUR'),

        //     'tax_rate' => env('SEVDESK_TAX_RATE', 19),
        //     'tax_text' => env('SEVDESK_TAX_TEXT', 'VAT 19%'),   // only in version 1.0
        //     'tax_type' => env('SEVDESK_TAX_TYPE', 'default'),   // only in version 1.0
        //     'tax_rule' => env('SEVDESK_TAX_RULE', 1),           // only in version 2.0
    ],
    /**
     * These are also necessary configs to create invoices or orders.
     */
    'invoice_email' => [
        /**
         * The default email address to send the invoice to.
         *
         * @phpstan-ignore larastan.noEnvCallsOutsideOfConfig
         */
        'subject' => env('SEVDESK_INVOICE_EMAIL_SUBJECT'),

        /**
         * The default email address to send the invoice to.
         *
         * @phpstan-ignore larastan.noEnvCallsOutsideOfConfig
         */
        'text' => env('SEVDESK_INVOICE_EMAIL_TEXT'),
    ],
];
