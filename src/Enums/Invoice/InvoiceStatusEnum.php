<?php

namespace mindtwo\LaravelSevdesk\Enums\Invoice;

enum InvoiceStatusEnum: int
{
    /**
     * Deactivated	The invoice is a deactivated recurring invoice.
     * This status code is only relevant for recurring invoices.
     */
    case DEACTIVATED = 50;

    /**
     * Draft	The invoice is still a draft.
     * It has not been sent to the end-customer and can still be changed.
     */
    case DRAFT = 100;

    /**
     * Open / Due	The invoice has been sent to the end-customer.
     * It is either shown as open if the pay date is not exceeded or due if it is.
     */
    case OPEN = 200;

    /**
     * Partially paid	The invoice has been partially paid.
     * This means, that it is linked to a transaction on some payment account in sevdesk.
     */
    case PARTIALLY_PAID = 750;

    /**
     * Paid	The invoice has been paid.
     * This means, that it is linked to a transaction on some payment account in sevdesk.
     */
    case PAID = 1000;
}
