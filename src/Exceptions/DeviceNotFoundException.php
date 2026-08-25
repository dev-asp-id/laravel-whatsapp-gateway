<?php

namespace Devaspid\WhatsappGateway\Exceptions;

class DeviceNotFoundException extends WhatsappGatewayException
{
    public function __construct(string $deviceId)
    {
        parent::__construct("Device '{$deviceId}' tidak ditemukan.", 404);
    }
}
