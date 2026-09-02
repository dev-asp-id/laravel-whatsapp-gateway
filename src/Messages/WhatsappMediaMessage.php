<?php

namespace Devaspid\WhatsappGateway\Messages;

use Devaspid\WhatsappGateway\DTOs\MessageResult;
use Devaspid\WhatsappGateway\Services\MessageService;

class WhatsappMediaMessage
{
    protected ?string $phone = null;
    protected ?string $deviceId = null;

    protected mixed $imageContent = null;
    protected mixed $videoContent = null;
    protected mixed $audioContent = null;
    protected mixed $fileContent = null;
    protected ?string $captionText = null;
    protected ?string $filenameText = null;
    protected ?bool $viewOnceFlag = null;

    public static function create(): static
    {
        return new static();
    }

    public function to(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function usingDevice(string $deviceId): static
    {
        $this->deviceId = $deviceId;

        return $this;
    }

    /**
     * Set file gambar (URL publik, Base64 data URI, path lokal, SplFileInfo, atau UploadedFile).
     */
    public function image(mixed $content): static
    {
        $this->imageContent = $content;

        return $this;
    }

    /**
     * Set file video (URL publik, Base64 data URI, path lokal, SplFileInfo, atau UploadedFile).
     */
    public function video(mixed $content): static
    {
        $this->videoContent = $content;

        return $this;
    }

    /**
     * Set file audio / voice note (URL publik, Base64 data URI, path lokal, SplFileInfo, atau UploadedFile).
     */
    public function audio(mixed $content): static
    {
        $this->audioContent = $content;

        return $this;
    }

    /**
     * Set dokumen / file (URL publik, Base64 data URI, path lokal, SplFileInfo, atau UploadedFile).
     */
    public function file(mixed $content, ?string $filename = null): static
    {
        $this->fileContent = $content;

        if ($filename !== null) {
            $this->filenameText = $filename;
        }

        return $this;
    }

    /**
     * Alias untuk file().
     */
    public function document(mixed $content, ?string $filename = null): static
    {
        return $this->file($content, $filename);
    }

    public function caption(string $caption): static
    {
        $this->captionText = $caption;

        return $this;
    }

    public function filename(string $filename): static
    {
        $this->filenameText = $filename;

        return $this;
    }

    public function viewOnce(bool $viewOnce = true): static
    {
        $this->viewOnceFlag = $viewOnce;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getDeviceId(): ?string
    {
        return $this->deviceId;
    }

    /**
     * Membuild payload media untuk dikirim ke API.
     */
    public function toMediaPayload(): array
    {
        return array_filter([
            'image'     => $this->imageContent,
            'video'     => $this->videoContent,
            'audio'     => $this->audioContent,
            'file'      => $this->fileContent,
            'caption'   => $this->captionText,
            'filename'  => $this->filenameText,
            'view_once' => $this->viewOnceFlag,
        ], fn ($v) => $v !== null);
    }

    /**
     * Kirim media menggunakan MessageService yang diberikan.
     */
    public function sendUsing(MessageService $service): MessageResult
    {
        return $service->sendMedia(
            phone: $this->phone,
            media: $this->toMediaPayload(),
            deviceId: $this->deviceId,
        );
    }
}
