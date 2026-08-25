<?php

namespace Devaspid\WhatsappGateway\Tests\Unit;

use Devaspid\WhatsappGateway\Facades\Whatsapp;
use Devaspid\WhatsappGateway\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class PingTest extends TestCase
{
    public function test_ping_returns_true_on_success(): void
    {
        Http::fake([
            'wa-gateway.test/api/v1/auth/ping' => Http::response([
                'success' => true,
                'message' => 'Authenticated successfully.',
                'data' => [
                    'client_id' => 'test-client-id',
                    'name' => 'Test Client',
                    'status' => 'active',
                ],
            ], 200),
        ]);

        $result = Whatsapp::ping();

        $this->assertTrue($result);
    }
}
