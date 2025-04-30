<?php

namespace mindtwo\LaravelSevdesk\Enums\Invoice;

enum InvoiceSendTypeEnum: string
{
    /**
     * Print
     */
    case PRINT = 'VPR';

    /**
     * Postal
     */
    case POSTAL = 'VP';

    /**
     * Mail
     */
    case MAIL = 'VM';

    /**
     * Downloaded PDF
     */
    case PDF = 'VPDF';
}
