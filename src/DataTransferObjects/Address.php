<?php

namespace mindtwo\LaravelSevdesk\DataTransferObjects;

use mindtwo\LaravelSevdesk\Facades\LaravelSevdesk;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class Address extends Data implements \ArrayAccess
{
    public function __construct(
        public string $name,
        public string $firstName,
        public string $lastName,
        public string $address1,
        public string $zip,
        public string $city,
        public string $country,
        public string $countryCode,
        public ?string $phone = null,
        public ?string $company = null,
        public ?string $address2 = null,
        public ?string $province = null,
    ) {}

    /**
     * Convert the address to an array that can be used for contacts.
     */
    public function toContactAddress(): array
    {
        $addressCountry = LaravelSevdesk::countries()->getStaticCountryByCode($this->countryCode);

        $address = [
            'street' => $this->address1,
            'zip' => $this->zip,
            'city' => $this->city,
            'country' => $addressCountry->toArray(),
            // TODO - how we get this?
            'category' => [
                'id' => 47,
                'objectName' => 'Category',
            ],
        ];

        // Add company if available
        if ($this->company === null) {
            $address['name'] = $this->name;
        } else {
            $address['name'] = $this->company;
            $address['name2'] = $this->name;
        }

        return $address;
    }

    /**
     * Convert the address to an array for an invoice.
     */
    public function toSevDeskInvoiceAddress(): array
    {
        $address = [
            'addressCity' => $this->city,
            'addressZip' => $this->zip,
            'addressStreet' => $this->address1,
        ];

        if ($this->company === null) {
            $address['addressName'] = $this->name;
        } else {
            $address['addressName'] = $this->company;
            $address['addressName2'] = $this->name;
        }

        return $address;
    }

    /**
     * Convert the address to an addressed person string.
     */
    public function getAddressName(): string
    {
        if ($this->company === null) {
            return $this->name;
        }

        return $this->company.' | '.$this->name;
    }

    /**
     * Convert the address to a string.
     */
    public function toAddressString(): string
    {
        $country = match ($this->country) {
            'Germany' => 'Deutschland',
            default => $this->country,
        };

        $address = $this->name."\n".$this->address1."\n".$this->zip.' '.$this->city."\n".$country;

        if ($this->company !== null) {
            $address = $this->company."\n".$address;
        }

        // Complete address of the recipient including name, street, city, zip and country. * Line breaks can be used and will be displayed on the invoice pdf.
        return $address;
    }

    /**
     * Add ArrayAccess methods
     */

    /**
     * OffsetExists
     */
    public function offsetExists($offset): bool
    {
        $arr = $this->toArray();

        return isset($arr[$offset]);
    }

    /**
     * OffsetGet
     */
    public function offsetGet($offset): mixed
    {
        $arr = $this->toArray();

        return $arr[$offset];
    }

    /**
     * OffsetSet
     */
    public function offsetSet($offset, $value): void
    {
        $arr = $this->toArray();

        $arr[$offset] = $value;
    }

    /**
     * OffsetUnset
     */
    public function offsetUnset($offset): void
    {
        if (! $this->offsetExists($offset)) {
            return;
        }

        $this->{$offset} = null;
    }
}
