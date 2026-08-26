<?php

namespace Devaspid\WhatsappGateway\DTOs;

class UserCheckResult
{
    public function __construct(
        public readonly bool $isOnWhatsapp,
        public readonly string $phone,
        public readonly ?string $jid = null,
    ) {}

    public static function fromArray(array $data): static
    {
        $payload = $data['data'] ?? $data;

        return new static(
            isOnWhatsapp: (bool) ($payload['is_on_whatsapp'] ?? false),
            phone: $payload['phone'] ?? '',
            jid: $payload['jid'] ?? null,
        );
    }

    /**
     * Memeriksa apakah nomor aktif/terdaftar di WhatsApp.
     */
    public function isOnWhatsapp(): bool
    {
        return $this->isOnWhatsapp;
    }

    /**
     * Alias untuk isOnWhatsapp().
     */
    public function isRegistered(): bool
    {
        return $this->isOnWhatsapp;
    }

    public function toArray(): array
    {
        return [
            'is_on_whatsapp' => $this->isOnWhatsapp,
            'phone'          => $this->phone,
            'jid'            => $this->jid,
        ];
    }
}
