<?php

namespace mindtwo\LaravelSevdesk\Enums\Contact;

enum ContactTypeEnum: int
{
    case SUPPLIER = 2;

    case CUSTOMER = 3;

    case PARTNER = 4;

    case PROSPECT_CUSTOMER = 28;

    public function toRequestArray(): array
    {
        return [
            'id' => $this->value,
            'objectName' => 'Category',
        ];
    }
}
