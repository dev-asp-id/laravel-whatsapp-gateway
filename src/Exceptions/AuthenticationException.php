<?php

namespace Devaspid\WhatsappGateway\Exceptions;

class AuthenticationException extends WhatsappGatewayException
{
    public function __construct(string $message = 'Autentikasi API gagal. Periksa WA_GATEWAY_CLIENT_ID dan WA_GATEWAY_API_KEY Anda.')
    {
        parent::__construct($message, 401);
    }
}
