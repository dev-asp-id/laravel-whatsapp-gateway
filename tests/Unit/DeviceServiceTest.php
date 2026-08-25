<?php

namespace Devaspid\WhatsappGateway\Tests\Unit;

use Devaspid\WhatsappGateway\DTOs\DeviceData;
use Devaspid\WhatsappGateway\DTOs\QrLoginResult;
use Devaspid\WhatsappGateway\Facades\Whatsapp;
use Devaspid\WhatsappGateway\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class DeviceServiceTest extends TestCase
{
    public function test_list_devices(): void
    {
        Http::fake([
            'wa-gateway.test/api/v1/devices' => Http::response([
                'success' => true,
                'data' => [
                    [
                        'id' => 'uuid-1',
                        'device_id' => 'dev_001',
                        'name' => 'CS WhatsApp',
                        'status' => 'connected',
                        'whatsapp_jid' => '6281234567890@s.whatsapp.net',
                        'last_connected_at' => '2026-08-25T00:15:00+07:00',
                        'created_at' => '2026-08-24T22:00:00+07:00',
                    ],
                ],
            ], 200),
        ]);

        $devices = Whatsapp::devices()->list();

        $this->assertCount(1, $devices);
        $this->assertInstanceOf(DeviceData::class, $devices[0]);
        $this->assertEquals('dev_001', $devices[0]->deviceId);
        $this->assertTrue($devices[0]->isConnected());
    }

    public function test_create_device(): void
    {
        Http::fake([
            'wa-gateway.test/api/v1/devices' => Http::response([
                'success' => true,
                'message' => 'Device slot created and registered.',
                'data' => [
                    'id' => 'uuid-new',
                    'device_id' => 'dev_new_001',
                    'name' => 'Sales WA',
                    'status' => 'disconnected',
                    'whatsapp_jid' => null,
                    'last_connected_at' => null,
                    'created_at' => '2026-08-25T00:20:00+07:00',
                ],
            ], 201),
        ]);

        $device = Whatsapp::devices()->create('Sales WA');

        $this->assertInstanceOf(DeviceData::class, $device);
        $this->assertEquals('dev_new_001', $device->deviceId);
        $this->assertEquals('disconnected', $device->status);
    }

    public function test_get_qr_code(): void
    {
        Http::fake([
            'wa-gateway.test/api/v1/devices/dev_001/login' => Http::response([
                'success' => true,
                'message' => 'QR code generated.',
                'data' => [
                    'device_id' => 'dev_001',
                    'qr_duration' => 20,
                    'qr_link' => 'http://example.com/qr.png',
                    'qr_image' => 'data:image/png;base64,iVBORw0KGgo...',
                ],
            ], 200),
        ]);

        $qr = Whatsapp::devices()->getQrCode('dev_001');

        $this->assertInstanceOf(QrLoginResult::class, $qr);
        $this->assertEquals('dev_001', $qr->deviceId);
        $this->assertStringStartsWith('data:image', $qr->imageUri);
        $this->assertEquals(20, $qr->qrDuration);
    }

    public function test_get_pairing_code(): void
    {
        Http::fake([
            'wa-gateway.test/api/v1/devices/dev_001/login/code' => Http::response([
                'success' => true,
                'message' => 'Pairing code generated.',
                'data' => [
                    'device_id' => 'dev_001',
                    'pair_code' => 'ABCD-1234',
                ],
            ], 200),
        ]);

        $code = Whatsapp::devices()->getPairingCode('dev_001', '6281234567890');

        $this->assertEquals('ABCD-1234', $code);
    }

    public function test_logout_device(): void
    {
        Http::fake([
            'wa-gateway.test/api/v1/devices/dev_001/logout' => Http::response([
                'success' => true,
                'message' => 'Device logged out.',
            ], 200),
        ]);

        $result = Whatsapp::devices()->logout('dev_001');

        $this->assertTrue($result);
    }

    public function test_reconnect_device(): void
    {
        Http::fake([
            'wa-gateway.test/api/v1/devices/dev_001/reconnect' => Http::response([
                'success' => true,
                'message' => 'Device reconnected.',
            ], 200),
        ]);

        $result = Whatsapp::devices()->reconnect('dev_001');

        $this->assertTrue($result);
    }

    public function test_delete_device(): void
    {
        Http::fake([
            'wa-gateway.test/api/v1/devices/dev_001' => Http::response([
                'success' => true,
                'message' => 'Device deleted.',
            ], 200),
        ]);

        $result = Whatsapp::devices()->delete('dev_001');

        $this->assertTrue($result);
    }
}
