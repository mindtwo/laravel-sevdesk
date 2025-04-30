<?php

namespace mindtwo\LaravelSevdesk;

use Exception;
use mindtwo\LaravelSevdesk\Api\CategoriesApi;
use mindtwo\LaravelSevdesk\Api\ContactsApi;
use mindtwo\LaravelSevdesk\Api\InvoicesApi;
use mindtwo\LaravelSevdesk\Api\StaticCountriesApi;

class LaravelSevdesk
{
    public function __construct(
        private ?string $apiToken = null,
        private ?string $sevUser = null,
        private ?string $checkAccount = null,
    ) {
        $this->apiToken = $this->apiToken ?? config('sevdesk.api_token');
        $this->sevUser = $this->sevUser ?? config('sevdesk.sev_user');
        $this->checkAccount = $this->checkAccount ?? config('sevdesk.check_account');

        if (empty($this->apiToken)) {
            throw new Exception('API token is required');
        }
    }

    /**
     * Get implementation for static countries API
     */
    public function countries(): StaticCountriesApi
    {
        return new StaticCountriesApi($this->apiToken);
    }

    /**
     * Get implementation for invoices API
     */
    public function invoices(): InvoicesApi
    {
        return new InvoicesApi($this->apiToken, $this->sevUser, $this->checkAccount);
    }

    /**
     * Get implementation for contacts API
     */
    public function contacts(): ContactsApi
    {
        return new ContactsApi($this->apiToken);
    }

    /**
     * Get implementation for categories API
     */
    public function categories(): CategoriesApi
    {
        return new CategoriesApi($this->apiToken);
    }
}
