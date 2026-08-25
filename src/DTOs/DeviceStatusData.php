<?php

namespace Devaspid\WhatsappGateway\DTOs;

class DeviceStatusData
{
    public function __construct(
        public readonly string $deviceId,
        public readonly string $status,
        public readonly ?string $whatsappJid,
        public readonly ?string $lastConnectedAt,
    ) {}

    public static function fromArray(array $data): static
    {
        $payload = $data['data'] ?? $data;

        return new static(
            deviceId: $payload['device_id'],
            status: $payload['status'],
            whatsappJid: $payload['whatsapp_jid'] ?? null,
            lastConnectedAt: $payload['last_connected_at'] ?? null,
        );
    }

    public function isConnected(): bool
    {
        return $this->status === 'connected';
    }

    public function toArray(): array
    {
        return [
            'device_id'         => $this->deviceId,
            'status'            => $this->status,
            'whatsapp_jid'      => $this->whatsappJid,
            'last_connected_at' => $this->lastConnectedAt,
        ];
    }
}
