<?php

namespace mindtwo\LaravelSevdesk\DataTransferObjects;

use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Castable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class Contact extends Data implements Castable
{
    public int $id;

    public string $objectName = 'Contact';

    public static function dataCastUsing(...$arguments): Cast
    {
        return new class implements Cast
        {
            public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
            {
                if (is_array($value)) {
                    return Contact::from($value);
                }

                if (is_numeric($value)) {
                    $instance = new Contact;
                    $instance->id = ! is_int($value) ? (int) $value : $value;

                    return $instance;
                }

                return null;
            }
        };
    }
}
