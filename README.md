# Laravel WhatsApp Gateway

[![Latest Version on Packagist](https://img.shields.io/packagist/v/devaspid/laravel-whatsapp-gateway.svg?style=flat-square)](https://packagist.org/packages/devaspid/laravel-whatsapp-gateway)
[![License](https://img.shields.io/packagist/l/devaspid/laravel-whatsapp-gateway.svg?style=flat-square)](https://packagist.org/packages/devaspid/laravel-whatsapp-gateway)

Package Laravel untuk integrasi dengan WhatsApp Gateway API [wa-api-by-asp](https://wa-gateway.asp.web.id/). Package ini menyediakan antarmuka yang bersih, modular, dan modern untuk mengirim pesan WhatsApp, mengelola device, serta integrasi penuh dengan Laravel Notification System.

## ✨ Fitur

- 📨 **Kirim Pesan Teks** — Quick send & Fluent API builder
- 🖼️ **Kirim Media** — Gambar, Audio/Voice Note, Dokumen (PDF/Excel/ZIP)
- 📱 **Device Management** — List, Create, QR Login, Pairing Code, Logout, Reconnect, Status
- 🔔 **Laravel Notification Channel** — Kirim notifikasi via `$user->notify()`
- 🛡️ **Typed Exceptions** — `AuthenticationException`, `ValidationException`, `DeviceNotFoundException`, dll.
- 📦 **Strongly-typed DTOs** — `MessageResult`, `DeviceData`, `QrLoginResult`
- ⚡ **Auto-discovery** — Service Provider & Facade otomatis terdaftar

## 📋 Persyaratan

- PHP 8.2+
- Laravel 10 / 11 / 12

## 🚀 Instalasi

```bash
composer require devaspid/laravel-whatsapp-gateway
```

Publish config file:

```bash
php artisan vendor:publish --tag=whatsapp-gateway-config
```

Tambahkan environment variables ke `.env`:

```env
WA_GATEWAY_BASE_URL=https://wa-gateway.asp.web.id/api/v1
WA_GATEWAY_CLIENT_ID=your-client-id
WA_GATEWAY_API_KEY=your-api-key
WA_GATEWAY_DEFAULT_DEVICE_ID=       # Opsional
WA_GATEWAY_TIMEOUT=15
WA_GATEWAY_RETRY_TIMES=2
WA_GATEWAY_RETRY_SLEEP=500
```

## 📖 Penggunaan

### Validasi Koneksi (Ping)

```php
use Devaspid\WhatsappGateway\Facades\Whatsapp;

if (Whatsapp::ping()) {
    echo 'API terhubung!';
}
```

### Kirim Pesan Teks

```php
use Devaspid\WhatsappGateway\Facades\Whatsapp;

// Quick send
$result = Whatsapp::send('6281234567890', 'Halo! Pesanan Anda telah dikonfirmasi.');

// Fluent API
$result = Whatsapp::to('6281234567890')
    ->usingDevice('dev_01m0xyz...')  // opsional
    ->replyTo('3EB0B430B6F...')      // opsional, quote reply
    ->message('Terima kasih telah berbelanja!')
    ->sendMessage();

// Cek hasil
if ($result->successful()) {
    echo 'Message ID: ' . $result->messageId();
}
```

### Kirim Media

#### Gambar dengan Caption
```php
Whatsapp::to('6281234567890')
    ->image('https://example.com/nota.png')
    ->caption('Bukti Pembayaran #INV-12345')
    ->viewOnce(false)
    ->sendMessage();
```

#### Dokumen PDF
```php
Whatsapp::to('6281234567890')
    ->file('https://example.com/laporan.pdf')
    ->filename('Laporan_Tahunan_2026.pdf')
    ->caption('Silakan unduh dokumen terlampir.')
    ->sendMessage();
```

#### Audio / Voice Note
```php
Whatsapp::to('6281234567890')
    ->audio('https://example.com/voice-greeting.mp3')
    ->sendMessage();
```

### Device Management

```php
// List semua devices
$devices = Whatsapp::devices()->list();

// Buat device baru
$device = Whatsapp::devices()->create('Customer Service WA');

// Detail device
$device = Whatsapp::devices()->find('dev_01m0xyz...');

// QR Code login
$qr = Whatsapp::devices()->getQrCode($device->deviceId);
echo $qr->toImgTag(); // <img src="data:image/png;base64,..." />

// Pairing Code
$code = Whatsapp::devices()->getPairingCode($device->deviceId, '6281234567890');
echo "Kode Pairing: {$code}";

// Status koneksi
$status = Whatsapp::devices()->getStatus($device->deviceId);
echo $status->isConnected() ? 'Online' : 'Offline';

// Reconnect, Logout, Delete
Whatsapp::devices()->reconnect($device->deviceId);
Whatsapp::devices()->logout($device->deviceId);
Whatsapp::devices()->delete($device->deviceId);
```

### Laravel Notification Channel

Buat notification class:

```php
namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Devaspid\WhatsappGateway\Channels\WhatsappChannel;
use Devaspid\WhatsappGateway\Messages\WhatsappMessage;

class InvoicePaidNotification extends Notification
{
    public function via($notifiable): array
    {
        return [WhatsappChannel::class];
    }

    public function toWhatsapp($notifiable): WhatsappMessage
    {
        return WhatsappMessage::create()
            ->to($notifiable->phone_number)
            ->message("Halo {$notifiable->name}, pembayaran Anda sebesar Rp 150.000 telah kami terima.");
    }
}
```

Gunakan dari User model:

```php
$user->notify(new InvoicePaidNotification());
```

> **Tip**: Anda bisa menambahkan method `routeNotificationForWhatsapp()` pada model Notifiable untuk mengkustomisasi nomor tujuan:
>
> ```php
> public function routeNotificationForWhatsapp(): string
> {
>     return $this->whatsapp_number;
> }
> ```

## ⚠️ Exception Handling

Package ini melempar exception terstruktur yang dapat di-catch:

```php
use Devaspid\WhatsappGateway\Exceptions\AuthenticationException;
use Devaspid\WhatsappGateway\Exceptions\ValidationException;
use Devaspid\WhatsappGateway\Exceptions\DeviceNotFoundException;
use Devaspid\WhatsappGateway\Exceptions\GatewayConnectionException;
use Devaspid\WhatsappGateway\Exceptions\RateLimitException;
use Devaspid\WhatsappGateway\Exceptions\WhatsappGatewayException;

try {
    Whatsapp::send('6281234567890', 'Hello!');
} catch (AuthenticationException $e) {
    // 401 — API key salah
} catch (ValidationException $e) {
    // 422 — Validasi gagal
    $errors = $e->getErrors(); // ['phone' => ['Format nomor tidak valid']]
} catch (DeviceNotFoundException $e) {
    // 404 — Device tidak ditemukan
} catch (RateLimitException $e) {
    // 429 — Terlalu banyak request
} catch (GatewayConnectionException $e) {
    // 502/503 — WA engine offline
} catch (WhatsappGatewayException $e) {
    // Catch-all untuk error lainnya
}
```

## 🧪 Testing

Jalankan test suite:

```bash
composer test
```

Atau langsung:

```bash
./vendor/bin/phpunit
```

Dalam project Anda, gunakan `Http::fake()` untuk mocking:

```php
use Illuminate\Support\Facades\Http;

Http::fake([
    'wa-gateway.asp.web.id/api/v1/messages' => Http::response([
        'success' => true,
        'data' => ['message_id' => 'FAKE_MSG_001', 'status' => 'success'],
    ]),
]);

$result = Whatsapp::send('6281234567890', 'Test message');
assertTrue($result->successful());
```

## 📁 Struktur Package

```
src/
├── Channels/
│   └── WhatsappChannel.php             # Laravel Notification Channel
├── Contracts/
│   └── WhatsappGatewayInterface.php    # Interface kontrak
├── DTOs/
│   ├── DeviceData.php                  # DTO Device
│   ├── DeviceStatusData.php            # DTO Status Device
│   ├── MessageResult.php               # DTO Hasil Pengiriman
│   └── QrLoginResult.php               # DTO QR Code
├── Exceptions/
│   ├── AuthenticationException.php     # 401
│   ├── DeviceNotFoundException.php     # 404
│   ├── GatewayConnectionException.php  # 502/503
│   ├── RateLimitException.php          # 429
│   ├── ValidationException.php         # 422
│   └── WhatsappGatewayException.php    # Base Exception
├── Facades/
│   └── Whatsapp.php                    # Facade
├── Messages/
│   ├── WhatsappMessage.php             # Fluent builder teks
│   └── WhatsappMediaMessage.php        # Fluent builder media
├── Services/
│   ├── DeviceService.php               # Device management
│   └── MessageService.php              # Pengiriman pesan
├── WhatsappClient.php                  # HTTP Client wrapper
├── WhatsappGateway.php                 # Core Manager
└── WhatsappServiceProvider.php         # Service Provider
config/
└── whatsapp-gateway.php                # File konfigurasi
```

## 🔧 Konfigurasi

File `config/whatsapp-gateway.php`:

| Key | Env Variable | Default | Keterangan |
|---|---|---|---|
| `base_url` | `WA_GATEWAY_BASE_URL` | `https://wa-gateway.asp.web.id/api/v1` | Base URL API |
| `client_id` | `WA_GATEWAY_CLIENT_ID` | `''` | Client ID dari dashboard |
| `api_key` | `WA_GATEWAY_API_KEY` | `''` | API Key dari dashboard |
| `default_device_id` | `WA_GATEWAY_DEFAULT_DEVICE_ID` | `null` | Device ID default |
| `timeout` | `WA_GATEWAY_TIMEOUT` | `15` | Timeout HTTP (detik) |
| `retry.times` | `WA_GATEWAY_RETRY_TIMES` | `2` | Jumlah retry |
| `retry.sleep` | `WA_GATEWAY_RETRY_SLEEP` | `500` | Jeda retry (ms) |

## 📄 Lisensi

MIT License. Lihat file [LICENSE](LICENSE) untuk detail.
