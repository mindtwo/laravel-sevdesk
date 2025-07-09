<?php

use mindtwo\LaravelSevdesk\Api\CategoriesApi;
use mindtwo\LaravelSevdesk\Api\ContactsApi;
use mindtwo\LaravelSevdesk\Api\InvoicesApi;
use mindtwo\LaravelSevdesk\Api\StaticCountriesApi;
use mindtwo\LaravelSevdesk\LaravelSevdesk;

if (! function_exists('sevdesk')) {
    /**
     * @param  ?string  $domain  - The domain we want to get the sevdesk instance for.
     * @return ($domain is 'contacts' ? ContactsApi : $domain is 'invoices' ? InvoicesApi : $domain is 'countries' ? StaticCountriesApi : $domain is 'categories' ? CategoriesApi : LaravelSevdesk)
     */
    function sevdesk(?string $domain = null)
    {
        $service = app(LaravelSevdesk::class);

        if ($domain && method_exists($service, $domain)) {
            return $service->$domain();
        }

        return $service;
    }
}
