<?php

namespace Devaspid\WhatsappGateway\Tests\Unit;

use Devaspid\WhatsappGateway\Exceptions\AuthenticationException;
use Devaspid\WhatsappGateway\Exceptions\DeviceNotFoundException;
use Devaspid\WhatsappGateway\Exceptions\RateLimitException;
use Devaspid\WhatsappGateway\Exceptions\ValidationException;
use Devaspid\WhatsappGateway\Facades\Whatsapp;
use Devaspid\WhatsappGateway\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class ExceptionHandlingTest extends TestCase
{
    public function test_throws_authentication_exception_on_401(): void
    {
        Http::fake([
            'wa-gateway.test/*' => Http::response(['message' => 'Unauthenticated.'], 401),
        ]);

        $this->expectException(AuthenticationException::class);

        Whatsapp::ping();
    }

    public function test_throws_device_not_found_on_404(): void
    {
        Http::fake([
            'wa-gateway.test/*' => Http::response(['message' => 'Device not found.'], 404),
        ]);

        $this->expectException(DeviceNotFoundException::class);

        Whatsapp::devices()->find('nonexistent-id');
    }

    public function test_throws_validation_exception_on_422(): void
    {
        Http::fake([
            'wa-gateway.test/*' => Http::response([
                'message' => 'The phone field format is invalid.',
                'errors' => [
                    'phone' => ['The phone field format is invalid.'],
                ],
            ], 422),
        ]);

        $this->expectException(ValidationException::class);

        Whatsapp::send('invalid', 'Hello');
    }

    public function test_throws_rate_limit_exception_on_429(): void
    {
        Http::fake([
            'wa-gateway.test/*' => Http::response(['message' => 'Too many requests.'], 429),
        ]);

        $this->expectException(RateLimitException::class);

        Whatsapp::send('6281234567890', 'Hello');
    }
}
