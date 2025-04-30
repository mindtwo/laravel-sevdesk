<?php

namespace mindtwo\LaravelSevdesk\Facades;

use Illuminate\Support\Facades\Facade;
use mindtwo\LaravelSevdesk\Api;

/**
 * @see \mindtwo\LaravelSevdesk\LaravelSevdesk
 *
 * @method static Api\StaticCountriesApi countries()
 * @method static Api\InvoicesApi invoices()
 * @method static Api\ContactsApi contacts()
 * @method static Api\CategoriesApi categories()
 * @method static Api\BaseApiService base()
 */
class LaravelSevdesk extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \mindtwo\LaravelSevdesk\LaravelSevdesk::class;
    }
}
