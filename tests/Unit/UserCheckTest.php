<?php

namespace Devaspid\WhatsappGateway\Tests\Unit;

use Devaspid\WhatsappGateway\DTOs\UserCheckResult;
use Devaspid\WhatsappGateway\Facades\Whatsapp;
use Devaspid\WhatsappGateway\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class UserCheckTest extends TestCase
{
    public function test_check_user_auto_device(): void
    {
        Http::fake([
            'wa-gateway.test/api/v1/user/check?phone=6281234567890' => Http::response([
                'success' => true,
                'message' => 'Phone checked successfully.',
                'data' => [
                    'is_on_whatsapp' => true,
                    'phone'          => '6281234567890',
                    'jid'            => '6281234567890@s.whatsapp.net',
                ],
            ], 200),
        ]);

        $result = Whatsapp::checkUser('6281234567890');

        $this->assertInstanceOf(UserCheckResult::class, $result);
        $this->assertTrue($result->isOnWhatsapp());
        $this->assertTrue($result->isRegistered());
        $this->assertEquals('6281234567890', $result->phone);
        $this->assertEquals('6281234567890@s.whatsapp.net', $result->jid);
    }

    public function test_is_registered_quick_helper(): void
    {
        Http::fake([
            'wa-gateway.test/api/v1/user/check?phone=6281234567890' => Http::response([
                'success' => true,
                'message' => 'Phone checked successfully.',
                'data' => [
                    'is_on_whatsapp' => true,
                    'phone'          => '6281234567890',
                    'jid'            => '6281234567890@s.whatsapp.net',
                ],
            ], 200),
        ]);

        $isRegistered = Whatsapp::isRegistered('6281234567890');

        $this->assertTrue($isRegistered);
    }

    public function test_check_user_specific_device(): void
    {
        Http::fake([
            'wa-gateway.test/api/v1/devices/dev_001/user/check?phone=6281234567890' => Http::response([
                'success' => true,
                'message' => 'Phone checked successfully.',
                'data' => [
                    'is_on_whatsapp' => false,
                    'phone'          => '6281234567890',
                    'jid'            => null,
                ],
            ], 200),
        ]);

        $result = Whatsapp::devices()->checkUser('dev_001', '6281234567890');

        $this->assertInstanceOf(UserCheckResult::class, $result);
        $this->assertFalse($result->isOnWhatsapp());
        $this->assertNull($result->jid);
    }

    public function test_check_user_via_user_service(): void
    {
        Http::fake([
            'wa-gateway.test/api/v1/user/check?phone=6281234567890' => Http::response([
                'success' => true,
                'message' => 'Phone checked successfully.',
                'data' => [
                    'is_on_whatsapp' => true,
                    'phone'          => '6281234567890',
                    'jid'            => '6281234567890@s.whatsapp.net',
                ],
            ], 200),
        ]);

        $result = Whatsapp::user()->check('6281234567890');

        $this->assertTrue($result->isOnWhatsapp());
    }
}
