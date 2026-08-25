<?php

namespace Devaspid\WhatsappGateway\DTOs;

class MessageResult
{
    public function __construct(
        public readonly string $messageId,
        public readonly string $status,
        public readonly bool $success,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            messageId: $data['data']['message_id'] ?? '',
            status: $data['data']['status'] ?? 'unknown',
            success: $data['success'] ?? false,
        );
    }

    public function successful(): bool
    {
        return $this->success && $this->status === 'success';
    }

    public function messageId(): string
    {
        return $this->messageId;
    }

    public function toArray(): array
    {
        return [
            'message_id' => $this->messageId,
            'status'     => $this->status,
            'success'    => $this->success,
        ];
    }
}
