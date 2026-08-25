<?php

namespace Devaspid\WhatsappGateway\Exceptions;

use RuntimeException;

class WhatsappGatewayException extends RuntimeException
{
    protected ?array $errors = null;

    public static function fromResponse(int $status, string $message, ?array $errors = null): static
    {
        $instance = new static($message, $status);
        $instance->errors = $errors;

        return $instance;
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }
}
