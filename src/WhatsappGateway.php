<?php

namespace Devaspid\WhatsappGateway;

use Devaspid\WhatsappGateway\Contracts\WhatsappGatewayInterface;
use Devaspid\WhatsappGateway\DTOs\MessageResult;
use Devaspid\WhatsappGateway\DTOs\UserCheckResult;
use Devaspid\WhatsappGateway\Messages\WhatsappMediaMessage;
use Devaspid\WhatsappGateway\Services\DeviceService;
use Devaspid\WhatsappGateway\Services\MessageService;
use Devaspid\WhatsappGateway\Services\UserService;

class WhatsappGateway implements WhatsappGatewayInterface
{
    protected WhatsappClient $client;
    protected MessageService $messageService;
    protected DeviceService $deviceService;
    protected UserService $userService;

    // Fluent builder state
    protected ?string $phone = null;
    protected ?string $deviceId = null;
    protected ?string $replyMessageId = null;
    protected ?string $content = null;

    // Media state
    protected mixed $imageContent = null;
    protected mixed $videoContent = null;
    protected mixed $audioContent = null;
    protected mixed $fileContent = null;
    protected ?string $captionText = null;
    protected ?string $filenameText = null;
    protected ?bool $viewOnceFlag = null;

    public function __construct(protected array $config)
    {
        $this->client         = new WhatsappClient($config);
        $this->messageService = new MessageService($this->client);
        $this->deviceService  = new DeviceService($this->client);
        $this->userService    = new UserService($this->client);
    }

    // ─── Quick Send / Terminal Fluent Send ──────────────────────────
 
    public function send(?string $phone = null, ?string $message = null, ?string $deviceId = null): MessageResult
    {
        if ($phone !== null && $message !== null) {
            $device = $deviceId ?? $this->config['default_device_id'] ?? null;

            return $this->messageService->sendText($phone, $message, $device);
        }

        if ($phone !== null) {
            $this->phone = $phone;
        }

        if ($deviceId !== null) {
            $this->deviceId = $deviceId;
        }

        return $this->sendMessage();
    }

    // ─── Fluent Builder ─────────────────────────────────────────────

    public function to(string $phone): static
    {
        $this->resetBuilder();
        $this->phone = $phone;

        return $this;
    }

    public function usingDevice(string $deviceId): static
    {
        $this->deviceId = $deviceId;

        return $this;
    }

    public function replyTo(string $messageId): static
    {
        $this->replyMessageId = $messageId;

        return $this;
    }

    public function message(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    // ─── Media Fluent ───────────────────────────────────────────────

    /**
     * Set file gambar (URL publik, Base64 data URI, file path lokal, SplFileInfo, atau UploadedFile).
     */
    public function image(mixed $content): static
    {
        $this->imageContent = $content;

        return $this;
    }

    /**
     * Set file video (URL publik, Base64 data URI, file path lokal, SplFileInfo, atau UploadedFile).
     */
    public function video(mixed $content): static
    {
        $this->videoContent = $content;

        return $this;
    }

    /**
     * Set file audio / voice note (URL publik, Base64 data URI, file path lokal, SplFileInfo, atau UploadedFile).
     */
    public function audio(mixed $content): static
    {
        $this->audioContent = $content;

        return $this;
    }

    /**
     * Set dokumen / file (URL publik, Base64 data URI, file path lokal, SplFileInfo, atau UploadedFile).
     */
    public function file(mixed $content, ?string $filename = null): static
    {
        $this->fileContent = $content;

        if ($filename !== null) {
            $this->filenameText = $filename;
        }

        return $this;
    }

    /**
     * Alias untuk file().
     */
    public function document(mixed $content, ?string $filename = null): static
    {
        return $this->file($content, $filename);
    }

    public function caption(string $caption): static
    {
        $this->captionText = $caption;

        return $this;
    }

    public function filename(string $filename): static
    {
        $this->filenameText = $filename;

        return $this;
    }

    public function viewOnce(bool $viewOnce = true): static
    {
        $this->viewOnceFlag = $viewOnce;

        return $this;
    }

    // ─── Send (Finalize Builder) ────────────────────────────────────

    /**
     * Mengirim pesan berdasarkan state fluent builder saat ini.
     * Otomatis mendeteksi apakah teks atau media.
     */
    public function sendMessage(): MessageResult
    {
        $device = $this->deviceId ?? $this->config['default_device_id'] ?? null;

        // Jika ada media, kirim sebagai media message
        if ($this->imageContent || $this->videoContent || $this->audioContent || $this->fileContent) {
            $media = (new WhatsappMediaMessage())
                ->to($this->phone);

            if ($device) {
                $media->usingDevice($device);
            }

            if ($this->imageContent) {
                $media->image($this->imageContent);
            }
            if ($this->videoContent) {
                $media->video($this->videoContent);
            }
            if ($this->audioContent) {
                $media->audio($this->audioContent);
            }
            if ($this->fileContent) {
                $media->file($this->fileContent);
            }
            if ($this->captionText !== null) {
                $media->caption($this->captionText);
            }
            if ($this->filenameText !== null) {
                $media->filename($this->filenameText);
            }
            if ($this->viewOnceFlag !== null) {
                $media->viewOnce($this->viewOnceFlag);
            }

            $result = $media->sendUsing($this->messageService);
            $this->resetBuilder();

            return $result;
        }

        // Kirim sebagai teks biasa
        $result = $this->messageService->sendText(
            phone: $this->phone,
            message: $this->content,
            deviceId: $device,
            replyMessageId: $this->replyMessageId,
        );

        $this->resetBuilder();

        return $result;
    }

    // ─── User Validation ────────────────────────────────────────────

    /**
     * Memvalidasi apakah suatu nomor telepon terdaftar di WhatsApp.
     */
    public function checkUser(string $phone, ?string $deviceId = null): UserCheckResult
    {
        $device = $deviceId ?? $this->config['default_device_id'] ?? null;

        return $this->userService->check($phone, $device);
    }

    /**
     * Cek cepat (boolean) apakah suatu nomor terdaftar di WhatsApp.
     */
    public function isRegistered(string $phone, ?string $deviceId = null): bool
    {
        return $this->checkUser($phone, $deviceId)->isOnWhatsapp();
    }

    // ─── Service Accessors ──────────────────────────────────────────

    public function devices(): DeviceService
    {
        return $this->deviceService;
    }

    public function messages(): MessageService
    {
        return $this->messageService;
    }

    public function user(): UserService
    {
        return $this->userService;
    }

    public function getClient(): WhatsappClient
    {
        return $this->client;
    }

    // ─── Auth Ping ──────────────────────────────────────────────────

    public function ping(): bool
    {
        $response = $this->client->get('/auth/ping');

        return ($response['success'] ?? false) === true;
    }

    // ─── Internal ───────────────────────────────────────────────────

    protected function resetBuilder(): void
    {
        $this->phone          = null;
        $this->deviceId       = null;
        $this->replyMessageId = null;
        $this->content        = null;
        $this->imageContent   = null;
        $this->videoContent   = null;
        $this->audioContent   = null;
        $this->fileContent    = null;
        $this->captionText    = null;
        $this->filenameText   = null;
        $this->viewOnceFlag   = null;
    }
}
