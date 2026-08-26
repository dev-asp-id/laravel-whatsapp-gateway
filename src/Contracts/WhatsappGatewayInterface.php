<?php

namespace Devaspid\WhatsappGateway\Contracts;

use Devaspid\WhatsappGateway\DTOs\MessageResult;
use Devaspid\WhatsappGateway\DTOs\UserCheckResult;
use Devaspid\WhatsappGateway\Services\DeviceService;
use Devaspid\WhatsappGateway\Services\MessageService;
use Devaspid\WhatsappGateway\Services\UserService;

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
     * Menambahkan video ke pesan media.
     */
    public function video(string $url): static;

    /**
     * Memvalidasi apakah nomor terdaftar di WhatsApp.
     */
    public function checkUser(string $phone, ?string $deviceId = null): UserCheckResult;

    /**
     * Cek boolean apakah nomor terdaftar di WhatsApp.
     */
    public function isRegistered(string $phone, ?string $deviceId = null): bool;

    /**
     * Mengakses Device Management Service.
     */
    public function devices(): DeviceService;

    /**
     * Mengakses Message Service.
     */
    public function messages(): MessageService;

    /**
     * Mengakses User Service.
     */
    public function user(): UserService;

    /**
     * Validasi koneksi dan API Key (ping).
     */
    public function ping(): bool;
}
