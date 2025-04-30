<?php

namespace mindtwo\LaravelSevdesk\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \mindtwo\LaravelSevdesk\LaravelSevdesk
 */
class LaravelSevdesk extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \mindtwo\LaravelSevdesk\LaravelSevdesk::class;
    }
}
