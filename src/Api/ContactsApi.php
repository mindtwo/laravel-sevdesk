<?php

namespace mindtwo\LaravelSevdesk\Api;

use Exception;
use mindtwo\LaravelSevdesk\Contracts\IsSevdeskCustomer;
use mindtwo\LaravelSevdesk\DataTransferObjects\Address;

class ContactsApi extends BaseApiService
{
    public function __construct(
        protected ?string $apiToken = null,
        protected ?string $sevUser = null,
        protected ?string $checkAccount = null,
    ) {
        parent::__construct($apiToken);

        if (empty($this->sevUser)) {
            throw new Exception('sevdesk user is required');
        }
    }

    /**
     * Get a contact from sevDesk
     *
     * @param  int|IsSevdeskCustomer  $customerOrSevdeskCustomerId  - The customer or the identifier of the sevdesk customer
     */
    public function getContact(int|IsSevdeskCustomer $customerOrSevdeskCustomerId): array
    {
        if ($customerOrSevdeskCustomerId instanceof IsSevdeskCustomer) {
            $customerOrSevdeskCustomerId = $customerOrSevdeskCustomerId->getSevdeskCustomerId();
        }

        $response = $this->client()->get("Contact/{$customerOrSevdeskCustomerId}");

        if (! $response->successful()) {
            throw $response->toException();
        }

        return $response->json('objects')[0] ?? [];
    }

    /**
     * Create a contact in sevDesk
     *
     * TODO - data dto
     */
    public function createContact(array $data): array
    {
        $response = $this->client()->post('Contact', $data);

        if (! $response->successful()) {
            throw $response->toException();
        }

        return $response->json('objects');
    }

    /**
     * Update a contact in sevDesk
     * TODO - data dto
     *
     * @param  int|IsSevdeskCustomer  $customerOrSevdeskCustomerId  - The customer or the identifier of the sevdesk customer
     */
    public function updateContact(int|IsSevdeskCustomer $customerOrSevdeskCustomerId, array $data): array
    {
        if ($customerOrSevdeskCustomerId instanceof IsSevdeskCustomer) {
            $customerOrSevdeskCustomerId = $customerOrSevdeskCustomerId->getSevdeskCustomerId();
        }

        $response = $this->client()->put("Contact/{$customerOrSevdeskCustomerId}", $data);

        if (! $response->successful()) {
            throw $response->toException();
        }

        return $response->json('objects');
    }

    /**
     * Get contact addresses from sevDesk for a customer
     *
     * @param  int|IsSevdeskCustomer  $customerOrSevdeskCustomerId  - The customer or the identifier of the sevdesk customer
     */
    public function getContactAddresses(int|IsSevdeskCustomer $customerOrSevdeskCustomerId): array
    {
        if ($customerOrSevdeskCustomerId instanceof IsSevdeskCustomer) {
            $customerOrSevdeskCustomerId = $customerOrSevdeskCustomerId->getSevdeskCustomerId();
        }

        $response = $this->client()->get('ContactAddress', [
            'contact[id]' => $customerOrSevdeskCustomerId,
            'contact[objectName]' => 'Contact',
        ]);

        if (! $response->successful()) {
            throw $response->toException();
        }

        return $response->json('objects');
    }

    /**
     * Create a contact address in sevDesk
     *
     * @param  int|IsSevdeskCustomer  $customerOrSevdeskCustomerId  - The customer or the identifier of the sevdesk customer
     * @param  array<string, mixed>|Address  $address  - The address or the address data
     */
    public function createContactAddress(int|IsSevdeskCustomer $customerOrSevdeskCustomerId, array|Address $address): array
    {
        if ($customerOrSevdeskCustomerId instanceof IsSevdeskCustomer) {
            $customerOrSevdeskCustomerId = $customerOrSevdeskCustomerId->getSevdeskCustomerId();
        }

        // Convert the address to sevDesk format
        $address = $address instanceof Address ? $address->toContactAddress() : $address;

        // Add the contact ID
        $data = [
            'contact' => [
                'id' => $customerOrSevdeskCustomerId,
                'objectName' => 'Contact',
            ],
            ...$address,
        ];

        $response = $this->client()->post('ContactAddress', $data);

        if (! $response->successful()) {
            throw $response->toException();
        }

        return $response->json('objects');
    }

    /**
     * Update a contact address in sevDesk
     *
     * @param  array<string, mixed>|Address  $address  - The address or the address data
     */
    public function updateContactAddress(int $contactAddressId, array|Address $address): array
    {
        // Convert the address to sevDesk format
        $address = $address instanceof Address ? $address->toContactAddress() : $address;

        $response = $this->client()->put("ContactAddress/{$contactAddressId}", $address);

        if (! $response->successful()) {
            throw $response->toException();
        }

        return $response->json('objects');
    }

    /**
     * Get communication ways from sevDesk
     *
     * @param  int|IsSevdeskCustomer  $customerOrSevdeskCustomerId  - The customer or the identifier of the sevdesk customer
     */
    public function getCommunicationWays(int|IsSevdeskCustomer $customerOrSevdeskCustomerId): array
    {
        $response = $this->client()->get('CommunicationWay', [
            'contact[id]' => $customerOrSevdeskCustomerId,
            'contact[objectName]' => 'Contact',
        ]);

        if (! $response->successful()) {
            throw $response->toException();
        }

        return $response->json('objects');
    }

    /**
     * Create a communication way in sevDesk for a customer
     *
     * @param  int|IsSevdeskCustomer  $customerOrSevdeskCustomerId  - The customer or the identifier of the sevdesk customer
     *
     * TODO type enum
     */
    public function createCommunicationWay(int|IsSevdeskCustomer $customerOrSevdeskCustomerId, string $value, string $type = 'EMAIL', bool $main = false): array
    {
        if ($customerOrSevdeskCustomerId instanceof IsSevdeskCustomer) {
            $customerOrSevdeskCustomerId = $customerOrSevdeskCustomerId->getSevdeskCustomerId();
        }

        // Data for communication way
        $data = [
            'contact' => [
                'id' => $customerOrSevdeskCustomerId,
                'objectName' => 'Contact',
            ],
            'type' => $type,
            'value' => $value,
            // 2 is the key for work communication / 1 is for private
            'key' => [
                'id' => 2,
                'objectName' => 'CommunicationWayKey',
            ],
            'main' => $main,
        ];

        $response = $this->client()->post('CommunicationWay', $data);

        if (! $response->successful()) {
            throw $response->toException();
        }

        return $response->json('objects');
    }

    /**
     * Update a communication way in sevDesk
     *
     * TODO type enum
     */
    public function updateCommunicationWay(int $communicationWayId, string $value, string $type = 'EMAIL', bool $main = false): array
    {
        // Data for communication way
        $data = [
            'type' => $type,
            'value' => $value,
            // 2 is the key for work communication / 1 is for private
            'key' => [
                'id' => 2,
                'objectName' => 'CommunicationWayKey',
            ],
            'main' => $main,
        ];

        $response = $this->client()->put("CommunicationWay/{$communicationWayId}", $data);

        if (! $response->successful()) {
            throw $response->toException();
        }

        return $response->json('objects');
    }
}
