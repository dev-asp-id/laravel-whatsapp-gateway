<?php

namespace Devaspid\WhatsappGateway\Facades;

use Devaspid\WhatsappGateway\DTOs\MessageResult;
use Devaspid\WhatsappGateway\DTOs\UserCheckResult;
use Devaspid\WhatsappGateway\Services\DeviceService;
use Devaspid\WhatsappGateway\Services\MessageService;
use Devaspid\WhatsappGateway\Services\UserService;
use Devaspid\WhatsappGateway\WhatsappGateway;
use Illuminate\Support\Facades\Facade;

/**
 * @method static MessageResult send(?string $phone = null, ?string $message = null, ?string $deviceId = null)
 * @method static WhatsappGateway to(string $phone)
 * @method static WhatsappGateway usingDevice(string $deviceId)
 * @method static WhatsappGateway replyTo(string $messageId)
 * @method static WhatsappGateway message(string $content)
 * @method static WhatsappGateway image(mixed $content)
 * @method static WhatsappGateway video(mixed $content)
 * @method static WhatsappGateway audio(mixed $content)
 * @method static WhatsappGateway file(mixed $content, ?string $filename = null)
 * @method static WhatsappGateway document(mixed $content, ?string $filename = null)
 * @method static WhatsappGateway caption(string $caption)
 * @method static WhatsappGateway filename(string $filename)
 * @method static WhatsappGateway viewOnce(bool $viewOnce = true)
 * @method static MessageResult sendMessage()
 * @method static UserCheckResult checkUser(string $phone, ?string $deviceId = null)
 * @method static bool isRegistered(string $phone, ?string $deviceId = null)
 * @method static DeviceService devices()
 * @method static MessageService messages()
 * @method static UserService user()
 * @method static bool ping()
 *
 * @see \Devaspid\WhatsappGateway\WhatsappGateway
 */
class Whatsapp extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return WhatsappGateway::class;
    }
}
