<?php

declare(strict_types=1);

namespace Jardis\Logger\command;

use Jardis\Logger\query\LogLevel;

/**
 * Returns log entries to sysLog
 */
class LogSyslog extends LogCommand
{
    /**
     * Constructor for initializing the logger with a specific log level.
     *
     * @param string $logLevel The logging level to be used (e.g., DEBUG, INFO, ERROR).
     * @return void
     */
    public function __construct(string $logLevel)
    {
        parent::__construct($logLevel);
        openlog($this->context(), LOG_PID, LOG_USER);
    }

    public function __destruct()
    {
        closelog();
        parent::__destruct();
    }

    protected function log(string $logMessage, array $logData): bool
    {
        $levelId = LogLevel::COLLECTION[$this->loglevel()];

        return syslog($levelId, $logMessage);
    }
}
