<?php

namespace mindtwo\LaravelSevdesk\DataTransferObjects;

use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class InvoicePosition extends Data
{
    public string $objectName = 'InvoicePos';

    #[Computed]
    public Unity $unity;

    public int|Optional $positionNumber;

    public function __construct(
        public string $name,
        public int $quantity,
        public float $price,
        public ?float $priceTax = null,
        public ?float $priceGross = null,
        public ?float $discount = null,
        public ?int $taxRate = 0,
        public ?string $text = null,
        public bool $mapAll = true,
        public mixed $optional = null,
        ?Unity $unity = null,
        null|int|Optional $positionNumber = null,
    ) {
        $this->unity = $unity ?? Unity::from([
            'id' => 1,
        ]);

        $this->positionNumber = $positionNumber ?? Optional::create();
    }
}
