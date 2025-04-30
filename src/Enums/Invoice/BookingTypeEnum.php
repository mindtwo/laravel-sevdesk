<?php

namespace mindtwo\LaravelSevdesk\Enums\Invoice;

enum BookingTypeEnum: string
{
    /**
     * Normal booking
     */
    case FULL_PAYMENT = 'FULL_PAYMENT';

    /**
     * Partial booking (historically used for normal booking)
     */
    case PARTIAL = 'N';

    /**
     * Reduced amount due to discount (skonto)
     */
    case DISCOUNT = 'CB';

    /**
     * Reduced/Higher amount due to currency fluctuations (deprecated)
     *
     * @deprecated
     */
    case CURRENCY_FLUCTUATION = 'CF';

    /**
     * Reduced/Higher amount due to other reasons
     */
    case OTHER = 'O';

    /**
     * Higher amount due to reminder charges
     */
    case REMINDER_CHARGES = 'OF';

    /**
     * Reduced amount due to the monetary traffic costs
     */
    case MONETARY_TRAFFIC_COSTS = 'MTC';
}
