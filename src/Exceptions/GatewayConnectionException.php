<?php

namespace Devaspid\WhatsappGateway\Exceptions;

class GatewayConnectionException extends WhatsappGatewayException
{
    public function __construct(string $message = 'WhatsApp Gateway tidak dapat dihubungi. Server mungkin offline atau terjadi timeout.')
    {
        parent::__construct($message, 503);
    }
}
