<?php

declare(strict_types=1);

namespace Jardis\Logger\service\history;

/**
 * Class responsible for managing a history log with categorized entries.
 */
class LogHistory implements LogHistoryInterface
{
    /** @var array<string, array<string, array<int, mixed>>> */
    private array $historyLog;
    private int $maxHistory;

    public function __construct(int $maxHistory = 10)
    {
        $this->maxHistory = $maxHistory;
        $this->historyLog = [];
    }

    public function add(string $level, string $class, $result): void
    {
        $this->historyLog[$level][$class][] = $result;
    }

    public function reduce(string $level, string $class): void
    {
        if (count($this->historyLog[$level][$class] ?? []) >= $this->maxHistory) {
            array_shift($this->historyLog[$level][$class]);
        }
    }

    public function level(?string $level = null): array
    {
        if ($level === null) {
            return $this->historyLog;
        }

        return array_key_exists($level, $this->historyLog) ? $this->historyLog[$level] : [];
    }
}
