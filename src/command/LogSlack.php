<?php

declare(strict_types=1);

namespace Jardis\Logger\command;

use Jardis\Logger\service\format\LogJsonFormat;

/**
 * Returns log entries to slack
 */
class LogSlack extends LogCommand
{
    private string $webhookUrl;

    /**
     * Constructor to initialize the logging service.
     *
     * @param string $logLevel The level of logging, e.g., 'error', 'warning', 'info'.
     * @param string $webhookUrl The URL where log messages will be sent.
     *
     * @return void
     */
    public function __construct(string $logLevel, string $webhookUrl)
    {
        $this->webhookUrl = $webhookUrl;
        $this->setFormat(new LogJsonFormat());

        parent::__construct($logLevel);
    }

    protected function log(string $logMessage, array $logData): bool
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => json_encode(['text' => $logMessage])
            ],
        ]);

        $response = $this->stream()
            ? parent::log($logMessage, $logData)
            : file_get_contents($this->webhookUrl, false, $context);

        return $response !== false;
    }
}
