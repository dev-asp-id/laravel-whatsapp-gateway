<?php

namespace Devaspid\WhatsappGateway\Messages;

use Devaspid\WhatsappGateway\DTOs\MessageResult;
use Devaspid\WhatsappGateway\Services\MessageService;

class WhatsappMessage
{
    protected ?string $phone = null;
    protected ?string $deviceId = null;
    protected ?string $replyMessageId = null;
    protected ?string $content = null;

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

    public function replyTo(string $messageId): static
    {
        $this->replyMessageId = $messageId;

        return $this;
    }

    public function message(string $content): static
    {
        $this->content = $content;

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

    public function getReplyMessageId(): ?string
    {
        return $this->replyMessageId;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Kirim pesan menggunakan MessageService yang diberikan.
     */
    public function sendUsing(MessageService $service): MessageResult
    {
        return $service->sendText(
            phone: $this->phone,
            message: $this->content,
            deviceId: $this->deviceId,
            replyMessageId: $this->replyMessageId,
        );
    }
}
