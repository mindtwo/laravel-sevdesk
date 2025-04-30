<?php

namespace mindtwo\LaravelSevdesk\DataTransferObjects\Casts;

use mindtwo\LaravelSevdesk\Enums\Invoice\TaxRuleEnum;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class TaxRuleEnumCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        if ($value === null) {
            return null;
        }

        if (is_array($value)) {
            return TaxRuleEnum::from($value['id']);
        }

        return TaxRuleEnum::from($value);
    }
}
