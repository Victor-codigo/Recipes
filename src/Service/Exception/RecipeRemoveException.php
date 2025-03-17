<?php

declare(strict_types=1);

namespace App\Service\Exception;

use Psr\Log\LogLevel;

class RecipeRemoveException extends ServiceException
{
    public function log(array $context = []): static
    {
        $this->createLog(LogLevel::INFO, $context);

        return $this;
    }
}
