<?php

namespace mindtwo\LaravelSevdesk\Enums\Invoice;

use Illuminate\Contracts\Support\Arrayable;

enum TaxRuleEnum: string implements Arrayable
{
    /**
     * Tax rule for normal sales.
     *
     * Allowed rates in positions:
     * - 0.0
     * - 7.0
     * - 19.0
     */
    case VAT_SUBJECTED = '1';

    /**
     * Tax rule for exports.
     *
     * Allowed rates in postions:
     * - 0.0
     */
    case EXPORTS = '2';

    /**
     * Tax rule for intra-community deliveries.
     *
     * Allowed rates in positions:
     * - 0.0
     * - 7.0
     * - 19.0
     */
    case INTRA_COMMUNITY_DELIVERIES = '3';

    /**
     * Tax rule for tax-free sales.
     *
     * Allowed rates in positions:
     * - 0.0
     */
    case TAX_FREE_SALES = '4';

    /**
     * Tax rule for reverse charge.
     *
     * Allowed rates in positions:
     * - 0.0
     */
    case REVERSE_CHARGE = '5';

    /**
     * Nicht im Inland steuerbare Leistung
     *
     * Allowed rates in positions:
     * - 0.0
     */
    case NOT_IN_COUNTRY_TAXABLE = '17';

    /**
     * One Stop Shop (goods)
     *
     * Allowed rates in positions:
     * - depending on country
     */
    case ONE_STOP_SHOP_GOODS = '18';

    /**
     * One Stop Shop (electronic service)
     *
     * Allowed rates in positions:
     * - depending on country
     */
    case ONE_STOP_SHOP_ELECTRONIC_SERVICE = '19';

    /**
     * One Stop Shop (other service)
     *
     * Allowed rates in positions:
     * - depending on country
     */
    case ONE_STOP_SHOP_OTHER_SERVICE = '20';

    public static function getAll(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get the instance as an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->value,
            'objectName' => 'TaxRule',
        ];
    }

    public function toRequestArray(): array
    {
        return [
            'id' => $this->value,
            'objectName' => 'TaxRule',
        ];
    }
}
