<?php

namespace Devaspid\WhatsappGateway\Services;

use Devaspid\WhatsappGateway\DTOs\UserCheckResult;
use Devaspid\WhatsappGateway\WhatsappClient;

class UserService
{
    public function __construct(protected WhatsappClient $client) {}

    /**
     * Memeriksa apakah suatu nomor telepon terdaftar di WhatsApp.
     */
    public function check(string $phone, ?string $deviceId = null): UserCheckResult
    {
        $endpoint = $deviceId
            ? "/devices/{$deviceId}/user/check"
            : '/user/check';

        $response = $this->client->get($endpoint, [
            'phone' => $phone,
        ]);

        return UserCheckResult::fromArray($response);
    }
}
