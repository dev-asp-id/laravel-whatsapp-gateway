<?php

namespace Devaspid\WhatsappGateway\Tests\Unit;

use Devaspid\WhatsappGateway\Facades\Whatsapp;
use Devaspid\WhatsappGateway\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use SplFileInfo;

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
            ->send();

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
            ->send();

        $this->assertTrue($result->successful());

        Http::assertSent(function ($request) {
            return $request['file'] === 'https://example.com/report.pdf'
                && $request['filename'] === 'Laporan_2026.pdf';
        });
    }

    public function test_send_document_using_alias(): void
    {
        Http::fake([
            'wa-gateway.test/api/v1/messages/media' => Http::response([
                'success' => true,
                'message' => 'Message sent successfully.',
                'data' => [
                    'message_id' => 'DOC_ALIAS_001',
                    'status' => 'success',
                ],
            ], 200),
        ]);

        $result = Whatsapp::to('6281234567890')
            ->document('https://example.com/invoice.pdf', 'Invoice_001.pdf')
            ->caption('Invoice Anda')
            ->send();

        $this->assertTrue($result->successful());

        Http::assertSent(function ($request) {
            return $request['file'] === 'https://example.com/invoice.pdf'
                && $request['filename'] === 'Invoice_001.pdf';
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
            ->send();

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
            ->send();

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

    public function test_send_base64_media_message(): void
    {
        Http::fake([
            'wa-gateway.test/api/v1/messages/media' => Http::response([
                'success' => true,
                'message' => 'Message sent successfully.',
                'data' => [
                    'message_id' => 'B64_MSG_001',
                    'status' => 'success',
                ],
            ], 200),
        ]);

        $base64 = 'data:application/pdf;base64,JVBERi0xLjQKJcTl8uXr...';

        $result = Whatsapp::to('6281234567890')
            ->file($base64, 'Invoice_Base64.pdf')
            ->caption('Bukti transaksi')
            ->send();

        $this->assertTrue($result->successful());

        Http::assertSent(function ($request) use ($base64) {
            return $request['file'] === $base64
                && $request['filename'] === 'Invoice_Base64.pdf';
        });
    }

    public function test_send_uploaded_file_multipart(): void
    {
        Http::fake([
            'wa-gateway.test/api/v1/messages/media' => Http::response([
                'success' => true,
                'message' => 'Message sent successfully.',
                'data' => [
                    'message_id' => 'UPLOAD_MSG_001',
                    'status' => 'success',
                ],
            ], 200),
        ]);

        $fakeFile = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $result = Whatsapp::to('6281234567890')
            ->file($fakeFile, 'Custom_Name.pdf')
            ->caption('File Upload')
            ->send();

        $this->assertTrue($result->successful());
        $this->assertEquals('UPLOAD_MSG_001', $result->messageId());

        Http::assertSent(function ($request) {
            return $request->isMultipart()
                && str_contains($request->url(), '/messages/media');
        });
    }

    public function test_send_spl_file_info_multipart(): void
    {
        Http::fake([
            'wa-gateway.test/api/v1/messages/media' => Http::response([
                'success' => true,
                'message' => 'Message sent successfully.',
                'data' => [
                    'message_id' => 'SPL_MSG_001',
                    'status' => 'success',
                ],
            ], 200),
        ]);

        $tempFile = tempnam(sys_get_temp_dir(), 'wa_test');
        file_put_contents($tempFile, 'test file content');

        $spl = new SplFileInfo($tempFile);

        $result = Whatsapp::to('6281234567890')
            ->file($spl, 'test_doc.txt')
            ->send();

        $this->assertTrue($result->successful());

        Http::assertSent(function ($request) {
            return $request->isMultipart();
        });

        @unlink($tempFile);
    }
}
