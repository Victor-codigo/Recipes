<?php

declare(strict_types=1);

namespace App\Controller\Exception;

class ControllerException extends \Exception
{
    final public function __construct(string $message, int $code, ?\Throwable $previous)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function fromMessage(string $message): static
    {
        return new static($message, 0,null);
    }
}
