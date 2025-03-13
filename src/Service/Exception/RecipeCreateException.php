<?php

declare(strict_types=1);

namespace App\Service\Exception;

use Psr\Log\LogLevel;

class RecipeCreateException extends ServiceException
{
    public function log(array $context = []): static
    {
        $this->createLog(LogLevel::INFO, $context);

        return $this;
    }
}
