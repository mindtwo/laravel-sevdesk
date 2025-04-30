<?php

namespace mindtwo\LaravelSevdesk\DataTransferObjects;

use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Castable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class StaticCountry extends Data implements Castable
{
    public int $id;

    public string $objectName = 'StaticCountry';

    public static function dataCastUsing(...$arguments): Cast
    {
        return new class implements Cast
        {
            public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
            {
                if (is_array($value)) {
                    return StaticCountry::from($value);
                }

                if (is_int($value)) {
                    $instance = new StaticCountry;
                    $instance->id = $value;

                    return $instance;
                }

                return null;
            }
        };
    }
}
