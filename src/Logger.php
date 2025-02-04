<?php

declare(strict_types=1);

namespace Jardis\Logger;

use Exception;
use Jardis\Logger\command\LogCommandInterface;
use Jardis\Logger\service\history\LogHistoryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel as PsrLogLevel;

/**
 * Logger class provides a flexible and extensible interface for logging messages
 * at various levels of severity. It implements the LoggerInterface and supports
 * adding multiple log command handlers.
 */
class Logger implements LoggerInterface
{
    private string $context;
    private ?LogHistoryInterface $logHistory;
    /** @var array<string, LogCommandInterface> $logCommand */
    private array $logCommand;

    public function __construct(string $context, ?LogHistoryInterface $logHistory = null)
    {
        $this->context = $context;
        $this->logHistory = $logHistory;
        $this->logCommand = [];
    }

    public function debug($message, array $context = array())
    {
        $this->log(PsrLogLevel::DEBUG, $message, $context);
    }

    public function info($message, array $context = array())
    {
        $this->log(PsrLogLevel::INFO, $message, $context);
    }

    public function notice($message, array $context = array())
    {
        $this->log(PsrLogLevel::NOTICE, $message, $context);
    }

    public function warning($message, array $context = array())
    {
        $this->log(PsrLogLevel::WARNING, $message, $context);
    }

    public function error($message, array $context = array())
    {
        $this->log(PsrLogLevel::ERROR, $message, $context);
    }

    public function critical($message, array $context = array())
    {
        $this->log(PsrLogLevel::CRITICAL, $message, $context);
    }
    public function alert($message, array $context = array())
    {
        $this->log(PsrLogLevel::ALERT, $message, $context);
    }

    public function emergency($message, array $context = array())
    {
        $this->log(PsrLogLevel::EMERGENCY, $message, $context);
    }

    public function log($level, $message, array $context = array())
    {
        if (empty($this->logCommand)) {
            return;
        }

        try {
            foreach ($this->logCommand as $class => $logCommand) {
                $result = $logCommand($level, $message, $context);
                if ($this->history()) {
                    $class = $this->className($logCommand);
                    $this->history()->reduce($level, $class);
                    $this->history()->add($level, $class, $result);
                }
            }
        } catch (Exception $e) {
            if ($this->history()) {
                $this->history()->add($level, $class, $e->getMessage());
            }
        }
    }

    /**
     * Adds a log command handler to the current log command interface.
     *
     * @param LogCommandInterface $logCommand The logging handler to be added.
     * @return self Returns the current instance for method chaining.
     */
    public function addLogger(LogCommandInterface $logCommand): self
    {
        $class = $this->className($logCommand);
        if (!empty($class) && !array_key_exists($class, $this->logCommand)) {
            $logCommand->setContext($this->context);
            $this->logCommand[$class] = $logCommand;
        }

        return $this;
    }

    /**
     * Retrieves the log history associated with the current instance.
     *
     * @return LogHistoryInterface|null The log history if available, or null.
     */
    public function history(): ?LogHistoryInterface
    {
        return $this->logHistory;
    }

    private function className(object $object): string
    {
        return basename(str_replace('\\', '/', get_class($object)));
    }
}
