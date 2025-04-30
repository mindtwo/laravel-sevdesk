<?php

namespace mindtwo\LaravelSevdesk\Api;

use Illuminate\Support\Facades\Cache;

class CategoriesApi extends BaseApiService
{
    /**
     * Get categories from sevDesk for a specific object type.
     *
     * @param  string  $objectType  - The object type to get categories for (e.g. 'ContactAddress', 'Part')
     */
    public function list(string $objectType): array
    {
        return Cache::remember('sevdesk:categories:'.$objectType, 60 * 60 * 24, function () use ($objectType) {

            $response = $this->client()->get('Category', [
                'objectType' => $objectType,
            ]);

            if (! $response->successful()) {
                throw $response->toException();
            }

            return $response->json('objects');
        });
    }
}
