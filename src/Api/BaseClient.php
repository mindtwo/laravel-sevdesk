<?php

namespace mindtwo\LaravelSevdesk\Api;

use Illuminate\Http\Client\PendingRequest;

/**
 * @mixin PendingRequest
 */
class BaseClient extends BaseApiService
{
    protected ?string $apiToken;

    public function __construct(string $apiToken)
    {
        if (! app()->runningInConsole()) {
            throw new \RuntimeException('This class should only be used in console commands.');
        }

        $this->apiToken = $apiToken;
    }

    public function __call($method, $parameters)
    {
        if (method_exists($this->client(), $method)) {
            return $this->client()->$method(...$parameters);
        }

        throw new \BadMethodCallException("Method {$method} does not exist on the client.");
    }
}
