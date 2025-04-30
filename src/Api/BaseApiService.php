<?php

namespace mindtwo\LaravelSevdesk\Api;

use Exception;
use Illuminate\Support\Facades\Http;

abstract class BaseApiService
{
    public function __construct(
        protected ?string $apiToken = null,
    ) {
        $this->apiToken = $this->apiToken ?? config('sevdesk.api_token');

        if (empty($this->apiToken)) {
            throw new Exception('API token is required');
        }
    }

    /**
     * Get http Client instance for sevdesk
     *
     * @return \Illuminate\Http\Client\PendingRequest
     */
    protected function client()
    {
        return Http::baseUrl('https://my.sevdesk.de/api/v1/')
            ->withHeaders([
                'Authorization' => $this->apiToken,
            ])
            ->timeout(10)
            ->retry(3, function (int $attempt, Exception $exception) {
                return $attempt * 100;
            })->acceptJson();
    }
}
