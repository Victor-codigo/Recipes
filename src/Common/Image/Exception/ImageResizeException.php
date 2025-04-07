<?php

declare(strict_types=1);

namespace App\Common\Image\Exception;

class ImageResizeException extends \Exception
{
    public static function fromMessage(string $message): self
    {
        return new self($message);
    }
}
