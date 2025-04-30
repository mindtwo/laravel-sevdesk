<?php

namespace mindtwo\LaravelSevdesk\Enums\Invoice;

enum InvoiceTypeEnum: string
{
    /**
     * A normal invoice which documents a simple selling process.
     */
    case NORMAL = 'RE';

    /**
     * An invoice which generates normal invoices with the same values regularly in fixed time frames (every month, year, ...).
     */
    case RECURRING = 'WKR';

    /**
     * An invoice which cancels another already created normal invoice.
     */
    case CANCELLATION = 'SR';

    /**
     * An invoice which gets created if the end-customer failed to pay a normal invoice in a given time frame.
     * Often includes some kind of reminder fee.
     */
    case REMINDER = 'MA';

    /**
     * Used when partial services have already been provided. One or more partial invoices result in a final invoice.
     */
    case PARTIAL = 'TR';

    /**
     * Used when no service has been provided yet. One or more advance invoices result in a final invoice.
     */
    case ADVANCE = 'AR';

    /**
     * The final invoice includes all partial / advance invoices.
     * After the final invoice is paid by the end-customer, the selling process is complete.
     */
    case FINAL = 'ER';
}
