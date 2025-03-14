<?php

declare(strict_types=1);

namespace App\Repository\Exception;

class RepositoryException extends \Exception
{
    final public function __construct(string $message, int $code, ?\Throwable $previous)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function fromMessage(string $message): static
    {
        return new static($message,0, null);
    }
}
