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
     * Menentukan device ID yang digunakan.
     */
    public function usingDevice(string $deviceId): static;

    /**
     * Mereply pesan tertentu berdasarkan message ID.
     */
    public function replyTo(string $messageId): static;

    /**
     * Set isi teks pesan.
     */
    public function message(string $content): static;

    /**
     * Menambahkan gambar ke pesan media (URL publik, Base64 data URI, file path lokal, SplFileInfo, atau UploadedFile).
     */
    public function image(mixed $content): static;

    /**
     * Menambahkan video ke pesan media (URL publik, Base64 data URI, file path lokal, SplFileInfo, atau UploadedFile).
     */
    public function video(mixed $content): static;

    /**
     * Menambahkan audio ke pesan media (URL publik, Base64 data URI, file path lokal, SplFileInfo, atau UploadedFile).
     */
    public function audio(mixed $content): static;

    /**
     * Menambahkan file/dokumen ke pesan media (URL publik, Base64 data URI, file path lokal, SplFileInfo, atau UploadedFile).
     */
    public function file(mixed $content, ?string $filename = null): static;

    /**
     * Alias untuk file().
     */
    public function document(mixed $content, ?string $filename = null): static;

    /**
     * Set caption teks pada media.
     */
    public function caption(string $caption): static;

    /**
     * Set nama file custom pada media.
     */
    public function filename(string $filename): static;

    /**
     * Set opsi view once pada media (gambar / video).
     */
    public function viewOnce(bool $viewOnce = true): static;

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
