<?php

declare(strict_types=1);

namespace Jardis\Logger\command;

/**
 * Returns log entries to console
 */
class LogConsole extends LogCommand
{
    public function __construct(string $logLevel)
    {
        $this->setStream(STDOUT);
        parent::__construct($logLevel);
    }
}
