<?php

namespace Devaspid\WhatsappGateway\DTOs;

class DeviceData
{
    public function __construct(
        public readonly string $id,
        public readonly string $deviceId,
        public readonly string $name,
        public readonly string $status,
        public readonly ?string $whatsappJid,
        public readonly ?string $lastConnectedAt,
        public readonly string $createdAt,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            id: $data['id'],
            deviceId: $data['device_id'],
            name: $data['name'],
            status: $data['status'],
            whatsappJid: $data['whatsapp_jid'] ?? null,
            lastConnectedAt: $data['last_connected_at'] ?? null,
            createdAt: $data['created_at'],
        );
    }

    public function isConnected(): bool
    {
        return $this->status === 'connected';
    }

    public function toArray(): array
    {
        return [
            'id'                => $this->id,
            'device_id'         => $this->deviceId,
            'name'              => $this->name,
            'status'            => $this->status,
            'whatsapp_jid'      => $this->whatsappJid,
            'last_connected_at' => $this->lastConnectedAt,
            'created_at'        => $this->createdAt,
        ];
    }
}
