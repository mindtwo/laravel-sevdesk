<?php

namespace mindtwo\LaravelSevdesk\Api;

use Exception;
use Illuminate\Support\Facades\Cache;
use mindtwo\LaravelSevdesk\DataTransferObjects\StaticCountry;

class StaticCountriesApi extends BaseApiService
{
    /**
     * Get the static country by code
     */
    public function getStaticCountryByCode(string $code): StaticCountry
    {
        // Cache the static country for 24 hours
        return Cache::remember('sevdesk:static-country:'.$code, 60 * 60 * 24, function () use ($code) {
            if (empty($code) || strlen($code) !== 2) {
                throw new Exception('Invalid country code');
            }

            // Get the static country by code
            try {
                $response = $this->getStaticCountries([
                    'code' => $code,
                ]);
            } catch (\Throwable $th) {
                $response = [];
            }

            if (empty($response) || ! isset($response[0])) {
                throw new Exception('Could not get static country for code '.$code);
            }

            // Return the static country
            return StaticCountry::from($response[0]);
        });
    }

    /**
     * Get the static countries
     */
    public function getStaticCountries(array $params = []): array
    {
        $response = $this->client()->get('StaticCountry', $params);

        if (! $response->successful()) {
            throw $response->toException();
        }

        return $response->json('objects');
    }
}
