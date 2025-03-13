<?php

declare(strict_types=1);

namespace App\Service\Exception;

use App\Common\Exception\LoggerException;

abstract class ServiceException extends LoggerException
{
    final public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function fromMessage(string $message): static
    {
        return new static($message, 0, null);
    }
}
