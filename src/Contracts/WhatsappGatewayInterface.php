<?php

namespace Devaspid\WhatsappGateway\Contracts;

use Devaspid\WhatsappGateway\DTOs\MessageResult;
use Devaspid\WhatsappGateway\Services\DeviceService;

interface WhatsappGatewayInterface
{
    /**
     * Mengirim pesan teks secara cepat atau mengeksekusi fluent message builder.
     */
    public function send(?string $phone = null, ?string $message = null, ?string $deviceId = null): MessageResult;

    /**
     * Alias untuk mengeksekusi fluent message builder.
     */
    public function sendMessage(): MessageResult;

    /**
     * Memulai fluent builder dengan nomor tujuan.
     */
    public function to(string $phone): static;

    /**
     * Mengakses Device Management Service.
     */
    public function devices(): DeviceService;

    /**
     * Validasi koneksi dan API Key (ping).
     */
    public function ping(): bool;
}
