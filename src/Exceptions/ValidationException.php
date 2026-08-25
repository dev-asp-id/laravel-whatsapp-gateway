<?php

namespace Devaspid\WhatsappGateway\Exceptions;

class ValidationException extends WhatsappGatewayException
{
    public static function fromErrors(array $errors): static
    {
        $first = collect($errors)->flatten()->first() ?? 'Validasi request gagal.';

        return static::fromResponse(422, $first, $errors);
    }
}
