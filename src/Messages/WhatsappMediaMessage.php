<?php

namespace Devaspid\WhatsappGateway\Messages;

use Devaspid\WhatsappGateway\DTOs\MessageResult;
use Devaspid\WhatsappGateway\Services\MessageService;

class WhatsappMediaMessage
{
    protected ?string $phone = null;
    protected ?string $deviceId = null;

    protected ?string $imageUrl = null;
    protected ?string $videoUrl = null;
    protected ?string $audioUrl = null;
    protected ?string $fileUrl = null;
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

    public function image(string $url): static
    {
        $this->imageUrl = $url;

        return $this;
    }

    public function video(string $url): static
    {
        $this->videoUrl = $url;

        return $this;
    }

    public function audio(string $url): static
    {
        $this->audioUrl = $url;

        return $this;
    }

    public function file(string $url): static
    {
        $this->fileUrl = $url;

        return $this;
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
            'image'     => $this->imageUrl,
            'video'     => $this->videoUrl,
            'audio'     => $this->audioUrl,
            'file'      => $this->fileUrl,
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
