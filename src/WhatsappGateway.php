<?php

namespace Devaspid\WhatsappGateway;

use Devaspid\WhatsappGateway\Contracts\WhatsappGatewayInterface;
use Devaspid\WhatsappGateway\DTOs\MessageResult;
use Devaspid\WhatsappGateway\Messages\WhatsappMediaMessage;
use Devaspid\WhatsappGateway\Services\DeviceService;
use Devaspid\WhatsappGateway\Services\MessageService;

class WhatsappGateway implements WhatsappGatewayInterface
{
    protected WhatsappClient $client;
    protected MessageService $messageService;
    protected DeviceService $deviceService;

    // Fluent builder state
    protected ?string $phone = null;
    protected ?string $deviceId = null;
    protected ?string $replyMessageId = null;
    protected ?string $content = null;

    // Media state
    protected ?string $imageUrl = null;
    protected ?string $audioUrl = null;
    protected ?string $fileUrl = null;
    protected ?string $captionText = null;
    protected ?string $filenameText = null;
    protected ?bool $viewOnceFlag = null;

    public function __construct(protected array $config)
    {
        $this->client         = new WhatsappClient($config);
        $this->messageService = new MessageService($this->client);
        $this->deviceService  = new DeviceService($this->client);
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

    public function image(string $url): static
    {
        $this->imageUrl = $url;

        return $this;
    }

    public function audio(string $url): static
    {
        $this->audioUrl = $url;

        return $this;
    }

    public function file(string $url): static
    {
        $this->fileUrl = $url;

        return $this;
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
        if ($this->imageUrl || $this->audioUrl || $this->fileUrl) {
            $media = (new WhatsappMediaMessage())
                ->to($this->phone);

            if ($device) {
                $media->usingDevice($device);
            }

            if ($this->imageUrl) {
                $media->image($this->imageUrl);
            }
            if ($this->audioUrl) {
                $media->audio($this->audioUrl);
            }
            if ($this->fileUrl) {
                $media->file($this->fileUrl);
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

    // ─── Service Accessors ──────────────────────────────────────────

    public function devices(): DeviceService
    {
        return $this->deviceService;
    }

    public function messages(): MessageService
    {
        return $this->messageService;
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
        $this->imageUrl       = null;
        $this->audioUrl       = null;
        $this->fileUrl        = null;
        $this->captionText    = null;
        $this->filenameText   = null;
        $this->viewOnceFlag   = null;
    }
}
