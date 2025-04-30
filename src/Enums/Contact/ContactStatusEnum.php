<?php

namespace mindtwo\LaravelSevdesk\Enums\Contact;

enum ContactStatusEnum: int
{
    case Lead = 100;

    case Pending = 500;

    case Active = 1000;

}
