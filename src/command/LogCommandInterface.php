<?php

declare(strict_types=1);

namespace Jardis\Logger\command;

use Jardis\Logger\service\format\LogFormatInterface;

interface LogCommandInterface
{
    /**
     * @param string $level
     * @param string $message
     * @param ?array<string, mixed> $data
     * @return string|array<string, mixed>|null
     */
    public function __invoke(string $level, string $message, ?array $data = []);

    public function setContext(string $context): self;

    public function setFormat(LogFormatInterface $logFormat): self;

    /** @param resource $stream */
    public function setStream($stream): self;
}
