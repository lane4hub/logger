<?php

declare(strict_types=1);

namespace Jardis\Logger\service\history;

interface LogHistoryInterface
{
    /**
     * Adds a result to the history log for a specific level and class.
     *
     * @param string $level The level to categorize the log entry.
     * @param string $class The class associated with the log entry.
     * @param mixed $result The result to be added to the history log.
     * @return void
     */
    public function add(string $level, string $class, $result): void;

    /**
     * Reduces the history log for the specified level and class by removing the oldest entry
     * if the maximum history limit is reached.
     *
     * @param string $level The log level to target (e.g., "error", "warning").
     * @param string $class The class name associated with the log entries.
     * @return void
     */
    public function reduce(string $level, string $class): void;

    /**
     * Retrieves the history log or a specific entry from it.
     *
     * @param string|null $level The index of the specific entry to retrieve.
     * If null, the entire history log is returned.
     * @return array<string, array<int, mixed>>|array<string, array<string, array<int, mixed>>> Returns the entire
     * history log as an array,
     * a specific entry as an array, or null
     * if the entry does not exist.
     */
    public function level(?string $level = null): array;
}
