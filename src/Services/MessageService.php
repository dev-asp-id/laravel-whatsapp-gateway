<?php

namespace Devaspid\WhatsappGateway\Services;

use Devaspid\WhatsappGateway\DTOs\MessageResult;
use Devaspid\WhatsappGateway\WhatsappClient;
use Illuminate\Http\UploadedFile;
use SplFileInfo;

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
     * Kirim pesan media (gambar/video/audio/file) dengan deteksi otomatis URL, Base64, atau Multipart file.
     */
    public function sendMedia(
        string $phone,
        array $media,
        ?string $deviceId = null,
    ): MessageResult {
        $endpoint = $deviceId
            ? "/devices/{$deviceId}/messages/media"
            : '/messages/media';

        $mediaTypes = ['image', 'video', 'audio', 'file'];
        $hasMultipart = false;
        $attachments = [];
        $formFields = array_filter([
            'phone'     => $phone,
            'caption'   => $media['caption'] ?? null,
            'filename'  => $media['filename'] ?? null,
            'view_once' => isset($media['view_once']) ? (bool) $media['view_once'] : null,
            'device_id' => $deviceId,
        ], fn ($v) => $v !== null);

        foreach ($mediaTypes as $type) {
            if (! isset($media[$type])) {
                continue;
            }

            $content = $media[$type];

            if ($this->isPhysicalFile($content)) {
                $hasMultipart = true;
                $attachments[] = $this->buildAttachment($type, $content, $media['filename'] ?? null);
            }
        }

        if ($hasMultipart) {
            $response = $this->client->postMultipart($endpoint, $formFields, $attachments);
        } else {
            $payload = array_filter(
                array_merge(['phone' => $phone, 'device_id' => $deviceId], $media),
                fn ($v) => $v !== null,
            );
            $response = $this->client->post($endpoint, $payload);
        }

        return MessageResult::fromArray($response);
    }

    /**
     * Cek apakah input berupa file fisik / stream untuk multipart upload.
     */
    protected function isPhysicalFile(mixed $content): bool
    {
        if ($content instanceof UploadedFile || $content instanceof SplFileInfo || is_resource($content)) {
            return true;
        }

        if (is_string($content)) {
            if (str_starts_with($content, 'http://') || str_starts_with($content, 'https://') || str_starts_with($content, 'data:')) {
                return false;
            }

            return is_file($content);
        }

        return false;
    }

    /**
     * Membangun array konfigurasi attachment untuk HTTP client.
     */
    protected function buildAttachment(string $name, mixed $content, ?string $customFilename = null): array
    {
        if ($content instanceof UploadedFile) {
            $filename = $customFilename ?: $content->getClientOriginalName();
            $stream = fopen($content->getRealPath(), 'r');
        } elseif ($content instanceof SplFileInfo) {
            $filename = $customFilename ?: $content->getFilename();
            $stream = fopen($content->getRealPath() ?: $content->getPathname(), 'r');
        } elseif (is_resource($content)) {
            $filename = $customFilename ?: "{$name}.bin";
            $stream = $content;
        } elseif (is_string($content) && is_file($content)) {
            $filename = $customFilename ?: basename($content);
            $stream = fopen($content, 'r');
        } else {
            $filename = $customFilename ?: "{$name}.bin";
            $stream = (string) $content;
        }

        return [
            'name'     => $name,
            'contents' => $stream,
            'filename' => $filename,
        ];
    }
}
