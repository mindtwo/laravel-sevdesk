<?php

use mindtwo\LaravelSevdesk\Api;
use mindtwo\LaravelSevdesk\LaravelSevdesk;

beforeEach(function () {
    config(['sevdesk.api_token' => 'test_api_token']);
    config(['sevdesk.sev_user' => 'test_sev_user']);
    config(['sevdesk.check_account' => 'test_check_account']);
});

it('can be instantiated', function () {
    $sevdesk = app(LaravelSevdesk::class);

    expect($sevdesk)->toBeInstanceOf(LaravelSevdesk::class);
});

it('can get the contacts API', function () {
    $sevdesk = app(LaravelSevdesk::class);
    $contactsApi = $sevdesk->contacts();

    expect($contactsApi)->toBeInstanceOf(Api\ContactsApi::class);
});

it('can get the invoices API', function () {
    $sevdesk = app(LaravelSevdesk::class);
    $invoicesApi = $sevdesk->invoices();

    expect($invoicesApi)->toBeInstanceOf(Api\InvoicesApi::class);
});

it('can get the categories API', function () {
    $sevdesk = app(LaravelSevdesk::class);
    $categoriesApi = $sevdesk->categories();

    expect($categoriesApi)->toBeInstanceOf(Api\CategoriesApi::class);
});

it('can get the static countries API', function () {
    $sevdesk = app(LaravelSevdesk::class);
    $countriesApi = $sevdesk->countries();

    expect($countriesApi)->toBeInstanceOf(Api\StaticCountriesApi::class);
});

it('throws an exception if API token is not set', function () {
    config(['sevdesk.api_token' => null]);

    $sevdesk = app(LaravelSevdesk::class);
})->throws(\Exception::class, 'API token is required');

it('throws an exception if sev_user is not set', function () {
    config(['sevdesk.sev_user' => null]);

    $sevdesk = app(LaravelSevdesk::class);
    $invoicesApi = $sevdesk->invoices();
})->throws(\Exception::class, 'sevdesk user is required');
