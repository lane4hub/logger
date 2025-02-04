<?php

declare(strict_types=1);

namespace Jardis\Logger\service\logData;

interface LogDataInterface
{
    /**
     * @param string $context log level.
     * @param string $level log level.
     * @param string $message message string for data.
     * @param ?array<string, mixed> $data additional information to the message.
     * @return array<string, mixed>
     */
    public function __invoke(string $context, string $level, string $message, ?array $data = null): array;

    /**
     * Adds a log data entry associated with a specific field name if it does not already exist.
     *
     * @param string $fieldName The name of the field to associate with the log data callback.
     * @param callable $logData A callback function that generates the log data for the specified field.
     * @return self The instance of the current object for method chaining.
     */
    public function addLogData(string $fieldName, callable $logData): self;

    /**
     * Adds a log data entry with a specified field name and its corresponding callable function.
     * The log data will only be added if the field name does not already exist.
     *
     * @param string $fieldName The name of the field to be logged.
     * @param callable $logData A callable that generates the data to be logged for the specified field.
     * @return self Returns the current instance for method chaining.
     */
    public function addUserLogData(string $fieldName, callable $logData): self;
}
