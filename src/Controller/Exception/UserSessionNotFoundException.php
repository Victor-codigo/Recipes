<?php

declare(strict_types=1);

namespace App\Controller\Exception;

use Psr\Log\LogLevel;

class UserSessionNotFoundException extends ControllerException
{
    public function log(array $context = []): static
    {
        $this->createLog(LogLevel::CRITICAL, $context);

        return $this;
    }
}
