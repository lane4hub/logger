<?php

declare(strict_types=1);

namespace Jardis\Logger\service\logData;

/**
 * Class Record is responsible for managing log data and generating log records
 * based on provided context, level, message, and additional data. It also allows
 * for dynamic additions of custom log data fields.
 */
class LogData implements LogDataInterface
{
    public const CONTEXT = 'context';
    public const LEVEL = 'level';
    public const MESSAGE = 'message';
    /** @var array<string, mixed>  */
    private array $additionalUserLogData = [];
    /** @var array<string, mixed>  */
    private array $recordLogData;

    /**
     * Constructor method to initialize the log record data.
     *
     * @param array<string, mixed> $additionalRecordLogData Additional data to be merged with the log record data.
     * @param array<string, mixed> $additionalUserLogData Additional data to be merged with the log user data.
     * @return void
     */
    public function __construct(?array $additionalRecordLogData = [], ?array $additionalUserLogData = [])
    {
        $this->recordLogData = array_merge($additionalRecordLogData ?? [], [
            static::CONTEXT => '',
            static::LEVEL => '',
            static::MESSAGE => '',
        ]);
        $this->additionalUserLogData = $additionalUserLogData ?? [];
    }

    public function __invoke(string $context, string $level, string $message, ?array $data = null): array
    {
        $logData = [];
        foreach ($this->recordLogData as $key => $value) {
            if (is_scalar($value) || is_array($value)) {
                if ($key === static::CONTEXT) {
                    $logData[$key] = $context;
                } elseif ($key === static::LEVEL) {
                    $logData[$key] = $level;
                } else {
                    $logData[$key] = $value;
                }
            } elseif (is_callable($value)) {
                $logData[$key] = $value();
            }
        }

        $data = array_merge($data ?? [], $this->additionalUserLogData);
        $logData[static::MESSAGE] = $this->interpolate($message, array_merge($data, $logData));
        $logData['data'] = $data;

        return $logData;
    }

    public function addLogData(string $fieldName, callable $logData): self
    {
        if (!array_key_exists($fieldName, $this->recordLogData)) {
            $this->recordLogData[$fieldName] = $logData;
        }

        return $this;
    }

    public function addUserLogData(string $fieldName, callable $logData): self
    {
        if (!array_key_exists($fieldName, $this->additionalUserLogData)) {
            $this->additionalUserLogData[$fieldName] = $logData;
        }

        return $this;
    }

    /**
     * Replaces placeholders in a message string with corresponding values from the provided data array.
     *
     * @param string $message The message containing placeholders in the format {key}.
     * @param array<string, mixed> $data An associative array where keys correspond to placeholders in the message,
     * and values are their replacements.
     * @return string The message with placeholders replaced by their corresponding values from the data array.
     */
    protected function interpolate(string $message, array $data): string
    {
        $replacements = [];
        foreach ($data as $key => $value) {
            if (is_scalar($value)) {
                $replacements['{' . $key . '}'] = $value;
            } elseif (is_array($value)) {
                $replacements['{' . $key . '}'] = json_encode($value);
            } elseif (is_callable($value)) {
                $replacements['{' . $key . '}'] = $value();
            }
        }

        return strtr($message, $replacements);
    }
}
