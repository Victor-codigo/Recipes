<?php

declare(strict_types=1);

namespace App\Repository\Exception;

use Psr\Log\LogLevel;

class DBNotFoundException extends RepositoryException
{
    public function log(array $context = []): static
    {
        $this->createLog(LogLevel::INFO, $context);

        return $this;
    }
}
