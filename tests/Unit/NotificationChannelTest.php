<?php

namespace Devaspid\WhatsappGateway\Tests\Unit;

use Devaspid\WhatsappGateway\Channels\WhatsappChannel;
use Devaspid\WhatsappGateway\Messages\WhatsappMediaMessage;
use Devaspid\WhatsappGateway\Messages\WhatsappMessage;
use Devaspid\WhatsappGateway\Tests\TestCase;
use Devaspid\WhatsappGateway\WhatsappGateway;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class NotificationChannelTest extends TestCase
{
    public function test_send_notification_via_whatsapp_channel(): void
    {
        Http::fake([
            'wa-gateway.test/api/v1/messages' => Http::response([
                'success' => true,
                'data' => [
                    'message_id' => 'NOTIF_MSG_001',
                    'status' => 'success',
                ],
            ], 200),
        ]);

        $channel = app(WhatsappChannel::class);
        $notifiable = new class {
            public string $phone_number = '6281234567890';
        };

        $notification = new class extends Notification {
            public function toWhatsapp($notifiable): WhatsappMessage
            {
                return WhatsappMessage::create()
                    ->message('Notifikasi testing');
            }
        };

        $channel->send($notifiable, $notification);

        Http::assertSent(function ($request) {
            return $request['phone'] === '6281234567890'
                && $request['message'] === 'Notifikasi testing';
        });
    }

    public function test_send_media_notification_via_whatsapp_channel(): void
    {
        Http::fake([
            'wa-gateway.test/api/v1/messages/media' => Http::response([
                'success' => true,
                'data' => [
                    'message_id' => 'NOTIF_MEDIA_001',
                    'status' => 'success',
                ],
            ], 200),
        ]);

        $channel = app(WhatsappChannel::class);
        $notifiable = new class {
            public function routeNotificationForWhatsapp(): string
            {
                return '6289876543210';
            }
        };

        $notification = new class extends Notification {
            public function toWhatsapp($notifiable): WhatsappMediaMessage
            {
                return WhatsappMediaMessage::create()
                    ->file('https://example.com/invoice.pdf')
                    ->filename('invoice.pdf')
                    ->caption('Invoice PDF');
            }
        };

        $channel->send($notifiable, $notification);

        Http::assertSent(function ($request) {
            return $request['phone'] === '6289876543210'
                && $request['file'] === 'https://example.com/invoice.pdf'
                && $request['filename'] === 'invoice.pdf'
                && $request['caption'] === 'Invoice PDF';
        });
    }
}
