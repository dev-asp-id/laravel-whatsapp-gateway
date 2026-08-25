<?php

namespace Devaspid\WhatsappGateway\Channels;

use Devaspid\WhatsappGateway\Messages\WhatsappMediaMessage;
use Devaspid\WhatsappGateway\Messages\WhatsappMessage;
use Devaspid\WhatsappGateway\WhatsappGateway;
use Illuminate\Notifications\Notification;

class WhatsappChannel
{
    public function __construct(protected WhatsappGateway $gateway) {}

    /**
     * Kirim notifikasi via WhatsApp.
     *
     * @param  mixed  $notifiable
     */
    public function send($notifiable, Notification $notification): void
    {
        // Coba panggil toWhatsapp() pada notification
        if (! method_exists($notification, 'toWhatsapp')) {
            return;
        }

        $message = $notification->toWhatsapp($notifiable);

        if ($message instanceof WhatsappMessage) {
            // Jika nomor belum di-set pada message, coba ambil dari notifiable
            $phone = $message->getPhone() ?? $this->getPhoneFromNotifiable($notifiable);

            if (! $phone) {
                return;
            }

            $deviceId = $message->getDeviceId();

            $this->gateway->send(
                phone: $phone,
                message: $message->getContent(),
                deviceId: $deviceId,
            );
        } elseif ($message instanceof WhatsappMediaMessage) {
            $phone = $message->getPhone() ?? $this->getPhoneFromNotifiable($notifiable);

            if (! $phone) {
                return;
            }

            $message->to($phone);
            $message->sendUsing($this->gateway->messages());
        }
    }

    /**
     * Coba ambil nomor telepon dari notifiable menggunakan konvensi umum.
     */
    protected function getPhoneFromNotifiable(mixed $notifiable): ?string
    {
        // Method khusus untuk routing WhatsApp notification
        if (method_exists($notifiable, 'routeNotificationForWhatsapp')) {
            return $notifiable->routeNotificationForWhatsapp();
        }

        // Fallback: property phone_number atau phone
        return $notifiable->phone_number ?? $notifiable->phone ?? null;
    }
}
