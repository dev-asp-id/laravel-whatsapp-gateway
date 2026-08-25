<?php

namespace Devaspid\WhatsappGateway\Services;

use Devaspid\WhatsappGateway\DTOs\MessageResult;
use Devaspid\WhatsappGateway\WhatsappClient;

class MessageService
{
    public function __construct(protected WhatsappClient $client) {}

    /**
     * Kirim pesan teks.
     */
    public function sendText(
        string $phone,
        string $message,
        ?string $deviceId = null,
        ?string $replyMessageId = null,
    ): MessageResult {
        $endpoint = $deviceId
            ? "/devices/{$deviceId}/messages"
            : '/messages';

        $payload = array_filter([
            'phone'            => $phone,
            'message'          => $message,
            'device_id'        => $deviceId,
            'reply_message_id' => $replyMessageId,
        ], fn ($v) => $v !== null);

        $response = $this->client->post($endpoint, $payload);

        return MessageResult::fromArray($response);
    }

    /**
     * Kirim pesan media (gambar/audio/file).
     */
    public function sendMedia(
        string $phone,
        array $media,
        ?string $deviceId = null,
    ): MessageResult {
        $endpoint = $deviceId
            ? "/devices/{$deviceId}/messages/media"
            : '/messages/media';

        $payload = array_filter(
            array_merge(['phone' => $phone, 'device_id' => $deviceId], $media),
            fn ($v) => $v !== null,
        );

        $response = $this->client->post($endpoint, $payload);

        return MessageResult::fromArray($response);
    }
}
