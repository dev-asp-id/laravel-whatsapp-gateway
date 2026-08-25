<?php

namespace Devaspid\WhatsappGateway\Exceptions;

class RateLimitException extends WhatsappGatewayException
{
    public function __construct(string $message = 'Rate limit API terlampaui. Terlalu banyak request dalam satu menit.')
    {
        parent::__construct($message, 429);
    }
}
