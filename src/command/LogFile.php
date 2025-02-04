<?php

declare(strict_types=1);

namespace Jardis\Logger\command;

use InvalidArgumentException;

/**
 * Returns log entries to common file
 */
class LogFile extends LogCommand
{
    /**
     * Constructor for initializing the logger with a specified log level and output file.
     *
     * @param string $logLevel The logging level, e.g., 'info', 'debug', etc.
     * @param string $file The file path where logs should be written.
     * @return void
     * @throws InvalidArgumentException If the directory for the provided file does not exist.
     */
    public function __construct(string $logLevel, string $file)
    {
        $directory = dirname($file);
        if (!is_dir($directory) || $directory === '.') {
            throw new InvalidArgumentException("Directory not found : " . $directory);
        }

        $stream = fopen($file, 'a');
        if ($stream) {
            $this->setStream($stream);
        }

        parent::__construct($logLevel);
    }
}
