<?php

declare(strict_types=1);

namespace Jardis\Logger\command;

use Jardis\Logger\query\LogLevel;
use Jardis\Logger\service\logData\LogData;
use Jardis\Logger\service\format\LogFormatInterface;
use Jardis\Logger\service\format\LogLineFormat;

/**
 * Handles the formatting and logging of messages with various levels of severity.
 * Implements the LogCommandInterface and provides functionality for setting log data,
 * formatting, streams, and determining responsibility for logging a given level.
 */
class LogCommand implements LogCommandInterface
{
    private string $context;
    private int $logLevel;
    private LogData $logData;
    private LogFormatInterface $logFormat;
    /** @var resource|null */
    private $stream = null;

    public function __construct(string $logLevel)
    {
        $this->logLevel = LogLevel::COLLECTION[strtolower($logLevel)] ?? 4;
        $this->context = '';
    }

    public function __invoke(string $level, string $message, ?array $data = []): ?string
    {
        if ($this->isResponsible($level)) {
            $logData = $this->logData()($this->context, $level, $message, $data);
            $logMessage = $this->format()($logData);
            return $this->log($logMessage, $logData) ? $logMessage : null;
        }

        return null;
    }

    /**
     * Logs a message with associated data to the current stream if available.
     *
     * @param string $logMessage The log message to be written.
     * @param array<string|int, mixed> $logData Additional data to be logged.
     * @return bool The number of bytes written to the stream, or false on failure.
     */
    protected function log(string $logMessage, array $logData): bool
    {
        if ($this->stream()) {
            return (bool) fwrite($this->stream(), $logMessage);
        }

        return false;
    }

    public function logData(): LogData
    {
        return $this->logData = $this->logData ?? new LogData();
    }

    public function setContext(string $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function setLogData(LogData $logData): self
    {
        $this->logData = $logData;

        return $this;
    }

    public function setFormat(LogFormatInterface $logFormat): self
    {
        $this->logFormat = $logFormat;

        return $this;
    }

    /** @param resource $stream */
    public function setStream($stream = null): self
    {
        $this->stream = $stream;

        return $this;
    }

    protected function context(): string
    {
        return $this->context;
    }

    protected function format(): LogFormatInterface
    {
        return $this->logFormat = $this->logFormat ?? new LogLineFormat();
    }

    /** @return  resource|null */
    protected function stream()
    {
        return $this->stream;
    }

    protected function isResponsible(string $level): bool
    {
        return $this->logLevel <= LogLevel::COLLECTION[strtolower($level)];
    }

    protected function loglevel(): string
    {
        $level = array_search($this->logLevel, LogLevel::COLLECTION);

        return is_string($level) ? $level : '';
    }

    public function __destruct()
    {
        $this->closeStream();
    }

    protected function closeStream(): void
    {
        if ($this->stream && is_resource($this->stream)) {
            fclose($this->stream);
        }
    }
}
