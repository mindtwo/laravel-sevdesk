<?php

namespace mindtwo\LaravelSevdesk\Contracts;

interface HasBuyerReference
{
    /**
     * Get the buyer reference for the customer.
     */
    public function getBuyerReference(): ?string;
}
