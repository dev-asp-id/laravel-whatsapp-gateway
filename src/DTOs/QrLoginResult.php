<?php

namespace Devaspid\WhatsappGateway\DTOs;

class QrLoginResult
{
    public function __construct(
        public readonly string $deviceId,
        public readonly int $qrDuration,
        public readonly string $imageUri,
        public readonly ?string $qrLink,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            deviceId: $data['data']['device_id'],
            qrDuration: $data['data']['qr_duration'] ?? 20,
            imageUri: $data['data']['qr_image'],
            qrLink: $data['data']['qr_link'] ?? null,
        );
    }

    /**
     * Mengembalikan string HTML <img> siap render.
     */
    public function toImgTag(string $alt = 'Scan QR Code', string $class = ''): string
    {
        $classAttr = $class ? " class=\"{$class}\"" : '';

        return "<img src=\"{$this->imageUri}\" alt=\"{$alt}\"{$classAttr} />";
    }

    public function toArray(): array
    {
        return [
            'device_id'    => $this->deviceId,
            'qr_duration'  => $this->qrDuration,
            'image_uri'    => $this->imageUri,
            'qr_link'      => $this->qrLink,
        ];
    }
}
