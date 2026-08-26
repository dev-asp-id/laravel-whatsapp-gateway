<?php

namespace Devaspid\WhatsappGateway\Tests\Unit;

use Devaspid\WhatsappGateway\Facades\Whatsapp;
use Devaspid\WhatsappGateway\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class SendMediaMessageTest extends TestCase
{
    public function test_send_image_message(): void
    {
        Http::fake([
            'wa-gateway.test/api/v1/messages/media' => Http::response([
                'success' => true,
                'message' => 'Message sent successfully.',
                'data' => [
                    'message_id' => 'IMG_MSG_001',
                    'status' => 'success',
                ],
            ], 200),
        ]);

        $result = Whatsapp::to('6281234567890')
            ->image('https://example.com/photo.png')
            ->caption('Bukti Pembayaran')
            ->viewOnce(false)
            ->sendMessage();

        $this->assertTrue($result->successful());
        $this->assertEquals('IMG_MSG_001', $result->messageId());

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/messages/media')
                && $request['phone'] === '6281234567890'
                && $request['image'] === 'https://example.com/photo.png'
                && $request['caption'] === 'Bukti Pembayaran'
                && $request['view_once'] === false;
        });
    }

    public function test_send_document_message(): void
    {
        Http::fake([
            'wa-gateway.test/api/v1/messages/media' => Http::response([
                'success' => true,
                'message' => 'Message sent successfully.',
                'data' => [
                    'message_id' => 'DOC_MSG_001',
                    'status' => 'success',
                ],
            ], 200),
        ]);

        $result = Whatsapp::to('6281234567890')
            ->file('https://example.com/report.pdf')
            ->filename('Laporan_2026.pdf')
            ->caption('Silakan unduh dokumen.')
            ->sendMessage();

        $this->assertTrue($result->successful());

        Http::assertSent(function ($request) {
            return $request['file'] === 'https://example.com/report.pdf'
                && $request['filename'] === 'Laporan_2026.pdf';
        });
    }

    public function test_send_audio_message(): void
    {
        Http::fake([
            'wa-gateway.test/api/v1/messages/media' => Http::response([
                'success' => true,
                'message' => 'Message sent successfully.',
                'data' => [
                    'message_id' => 'AUD_MSG_001',
                    'status' => 'success',
                ],
            ], 200),
        ]);

        $result = Whatsapp::to('6281234567890')
            ->audio('https://example.com/voice.mp3')
            ->sendMessage();

        $this->assertTrue($result->successful());

        Http::assertSent(function ($request) {
            return $request['audio'] === 'https://example.com/voice.mp3';
        });
    }

    public function test_send_video_message(): void
    {
        Http::fake([
            'wa-gateway.test/api/v1/messages/media' => Http::response([
                'success' => true,
                'message' => 'Message sent successfully.',
                'data' => [
                    'message_id' => 'VID_MSG_001',
                    'status' => 'success',
                ],
            ], 200),
        ]);

        $result = Whatsapp::to('6281234567890')
            ->video('https://example.com/video.mp4')
            ->caption('Tutorial Video')
            ->viewOnce(false)
            ->sendMessage();

        $this->assertTrue($result->successful());
        $this->assertEquals('VID_MSG_001', $result->messageId());

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/messages/media')
                && $request['phone'] === '6281234567890'
                && $request['video'] === 'https://example.com/video.mp4'
                && $request['caption'] === 'Tutorial Video'
                && $request['view_once'] === false;
        });
    }
}
