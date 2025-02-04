<?php

declare(strict_types=1);

namespace Jardis\Logger\service\format;

use InvalidArgumentException;
use DateTime;

/**
 * Returns default format for log entries
 */
class LogLineFormat implements LogFormatInterface
{
    /**
     * @param array<string, mixed> $logData
     * @return string
     */
    public function __invoke(array $logData): string
    {
        try {
            $result = sprintf($this->logFormat($logData), ...$this->logValues($logData));
        } catch (\Throwable $e) {
            throw new InvalidArgumentException('Invalid log data provided.', 0, $e);
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $logData
     * @return string
     */
    private function logFormat(array $logData): string
    {
        $parts = [];
        foreach (array_keys($logData) as $key) {
            $parts[] = "\"$key\": \"%s\"";
        }

        return '{ ' . implode(', ', $parts) . ' }' . "\n";
    }

    /**
     * @param array<string, mixed> $logData
     * @return array<int, mixed>
     */
    private function logValues(array $logData): array
    {
        $result = [];
        foreach ($logData as $value) {
            if (is_scalar($value)) {
                $result[] = $value;
            } elseif (is_array($value)) {
                $result[] = json_encode($value);
            } elseif (is_callable($value)) {
                $result[] = $value();
            } elseif ($value instanceof DateTime) {
                $result[] = $value->format('Y-m-d H:i:s');
            } elseif (is_object($value)) {
                $result[] = serialize($value);
            }
        }

        return $result;
    }
}
