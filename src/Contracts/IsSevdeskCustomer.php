<?php

namespace mindtwo\LaravelSevdesk\Contracts;

/**
 * Interface for models that have a sevdesk customer ID.
 */
interface IsSevdeskCustomer
{
    /**
     * Get the sevdesk customer ID.
     */
    public function getSevdeskCustomerId(): int|string;

    /**
     * Get the column name for the sevdesk customer ID.
     */
    public function getSevdeskCustomerIdColumn(): string;
}
