<?php

declare(strict_types=1);

namespace App\Common\Exception;

use Psr\Log\LogLevel;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

abstract class LoggerException extends \Exception implements LoggerAwareInterface
{
    private LoggerInterface $logger;

    /**
     * @param array<array-key, mixed> $context
     */
    abstract public function log(array $context = []): static;

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param string                  $loggerLevel One of the constants LogLevel::*
     * @param array<array-key, mixed> $context
     */
    protected function createLog(string $loggerLevel, array $context = []): static
    {
        match ($loggerLevel) {
            default => throw new \LogicException('Wrong error level'),
            LogLevel::ALERT => $this->logger->alert($this->getMessage(), $context),
            LogLevel::CRITICAL => $this->logger->critical($this->getMessage(), $context),
            LogLevel::DEBUG => $this->logger->debug($this->getMessage(), $context),
            LogLevel::EMERGENCY => $this->logger->emergency($this->getMessage(), $context),
            LogLevel::ERROR => $this->logger->error($this->getMessage(), $context),
            LogLevel::INFO => $this->logger->info($this->getMessage(), $context),
            LogLevel::NOTICE => $this->logger->notice($this->getMessage(), $context),
            LogLevel::WARNING => $this->logger->warning($this->getMessage(), $context),
        };

        return $this;
    }
}
