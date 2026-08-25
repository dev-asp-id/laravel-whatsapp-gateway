<?php

namespace Devaspid\WhatsappGateway\Tests\Unit;

use Devaspid\WhatsappGateway\Facades\Whatsapp;
use Devaspid\WhatsappGateway\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class SendTextMessageTest extends TestCase
{
    public function test_send_text_message_quick(): void
    {
        Http::fake([
            'wa-gateway.test/api/v1/messages' => Http::response([
                'success' => true,
                'message' => 'Message sent successfully.',
                'data' => [
                    'message_id' => '3EB0B430B6F8F1D0E053',
                    'status' => 'success',
                ],
            ], 200),
        ]);

        $result = Whatsapp::send('6281234567890', 'Hello World');

        $this->assertTrue($result->successful());
        $this->assertEquals('3EB0B430B6F8F1D0E053', $result->messageId());

        Http::assertSent(function ($request) {
            return $request->url() === 'https://wa-gateway.test/api/v1/messages'
                && $request['phone'] === '6281234567890'
                && $request['message'] === 'Hello World'
                && $request->hasHeader('X-Client-Id', 'test-client-id')
                && $request->hasHeader('X-Api-Key', 'test-api-key');
        });
    }

    public function test_send_text_with_fluent_api(): void
    {
        Http::fake([
            'wa-gateway.test/api/v1/devices/dev_123/messages' => Http::response([
                'success' => true,
                'message' => 'Message sent successfully.',
                'data' => [
                    'message_id' => 'MSG_FLUENT_001',
                    'status' => 'success',
                ],
            ], 200),
        ]);

        $result = Whatsapp::to('6281234567890')
            ->usingDevice('dev_123')
            ->replyTo('REPLY_ID_001')
            ->message('Terima kasih!')
            ->sendMessage();

        $this->assertTrue($result->successful());
        $this->assertEquals('MSG_FLUENT_001', $result->messageId());

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/devices/dev_123/messages')
                && $request['phone'] === '6281234567890'
                && $request['message'] === 'Terima kasih!'
                && $request['reply_message_id'] === 'REPLY_ID_001';
        });
    }

    public function test_send_text_with_fluent_send_terminal(): void
    {
        Http::fake([
            'wa-gateway.test/api/v1/messages' => Http::response([
                'success' => true,
                'message' => 'Message sent successfully.',
                'data' => [
                    'message_id' => 'MSG_FLUENT_SEND_001',
                    'status' => 'success',
                ],
            ], 200),
        ]);

        $result = Whatsapp::to('6281234567890')
            ->message('Pesan via fluent send()')
            ->send();

        $this->assertTrue($result->successful());
        $this->assertEquals('MSG_FLUENT_SEND_001', $result->messageId());
    }
}
