<?php

declare(strict_types=1);

namespace App\Common\Logger;

use Monolog\Attribute\AsMonologProcessor;
use Monolog\LogRecord;
use Random\Randomizer;

#[AsMonologProcessor]
class RequestIdProcessor
{
    private static string $requestId;

    public function __invoke(LogRecord $record): LogRecord
    {
        $this->setRequestId();
        $record->extra['requestId'] = self::$requestId;

        return $record;
    }

    private function setRequestId(): void
    {
        if (!isset(self::$requestId)) {
            self::$requestId = new Randomizer()->getBytesFromString('mñaskjfbapornha', 5);
        }
    }
}
