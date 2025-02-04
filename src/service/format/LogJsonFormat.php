<?php

declare(strict_types=1);

namespace Jardis\Logger\service\format;

use JsonException;

/**
 * Returns json format for log entries
 */
class LogJsonFormat implements LogFormatInterface
{
    /**
     * @param array<string, mixed> $logData
     * @return string
     * @throws JsonException
     */
    public function __invoke(array $logData): string
    {
        return json_encode($logData, JSON_THROW_ON_ERROR);
    }
}
