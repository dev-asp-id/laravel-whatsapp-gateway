<?php

namespace Devaspid\WhatsappGateway\Services;

use Devaspid\WhatsappGateway\DTOs\DeviceData;
use Devaspid\WhatsappGateway\DTOs\DeviceStatusData;
use Devaspid\WhatsappGateway\DTOs\QrLoginResult;
use Devaspid\WhatsappGateway\WhatsappClient;

class DeviceService
{
    public function __construct(protected WhatsappClient $client) {}

    /**
     * Mengambil semua slot device yang dimiliki client ini.
     *
     * @return DeviceData[]
     */
    public function list(): array
    {
        $response = $this->client->get('/devices');

        return array_map(
            fn (array $item) => DeviceData::fromArray($item),
            $response['data'] ?? []
        );
    }

    /**
     * Membuat slot device WhatsApp baru.
     */
    public function create(string $name): DeviceData
    {
        $response = $this->client->post('/devices', ['name' => $name]);

        return DeviceData::fromArray($response['data']);
    }

    /**
     * Mengambil detail satu slot device.
     */
    public function find(string $device): DeviceData
    {
        $response = $this->client->get("/devices/{$device}");

        return DeviceData::fromArray($response['data']);
    }

    /**
     * Menghapus permanen slot device dari server.
     */
    public function delete(string $device): bool
    {
        $response = $this->client->delete("/devices/{$device}");

        return $response['success'] ?? false;
    }

    /**
     * Mendapatkan QR Code login untuk di-scan via WhatsApp.
     */
    public function getQrCode(string $device): QrLoginResult
    {
        $response = $this->client->post("/devices/{$device}/login");

        return QrLoginResult::fromArray($response);
    }

    /**
     * Mendapatkan 8-digit Pairing Code untuk nomor telepon tertentu.
     */
    public function getPairingCode(string $device, string $phone): string
    {
        $response = $this->client->post("/devices/{$device}/login/code", [
            'phone' => $phone,
        ]);

        return $response['data']['pair_code'];
    }

    /**
     * Logout sesi WhatsApp (slot device tetap tersimpan).
     */
    public function logout(string $device): bool
    {
        $response = $this->client->post("/devices/{$device}/logout");

        return $response['success'] ?? false;
    }

    /**
     * Reconnect sesi WhatsApp yang terputus.
     */
    public function reconnect(string $device): bool
    {
        $response = $this->client->post("/devices/{$device}/reconnect");

        return $response['success'] ?? false;
    }

    /**
     * Mengambil status koneksi live dari device.
     */
    public function getStatus(string $device): DeviceStatusData
    {
        $response = $this->client->get("/devices/{$device}/status");

        return DeviceStatusData::fromArray($response);
    }
}
