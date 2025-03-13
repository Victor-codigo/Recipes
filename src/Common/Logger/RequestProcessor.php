<?php

declare(strict_types=1);

namespace App\Common\Logger;

use Monolog\Attribute\AsMonologProcessor;
use Monolog\LogRecord;
use Symfony\Component\HttpFoundation\RequestStack;

#[AsMonologProcessor('request')]
class RequestProcessor
{
    private const int REQUEST_VALUES_LIMIT = 50;

    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return $record;
        }

        $record->extra['request'] = [
            'request' => array_slice($request->request->all(), 0, self::REQUEST_VALUES_LIMIT, true),
            'attributes' => array_slice($request->attributes->all(), 0, self::REQUEST_VALUES_LIMIT, true),
            'query' => array_slice($request->query->all(), 0, self::REQUEST_VALUES_LIMIT, true),
            'files' => array_slice($request->files->all(), 0, self::REQUEST_VALUES_LIMIT, true),
        ];

        return $record;
    }
}
