<?php

namespace Jardis\Logger\Tests\unit\command;

use Jardis\Logger\command\LogErrorLog;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class LogErrorLogTest extends TestCase
{
    private string $filePath = __DIR__ . '/../../tmp/error.log';
    protected function tearDown(): void
    {
        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }
    }

    public function testWritesToStream(): void
    {
        $logger = new LogErrorLog(LogLevel::INFO);

        $stream = fopen($this->filePath, 'a');
        if ($stream) {
            $logger->setStream($stream);

            $result = $logger(LogLevel::INFO, 'Test message', ['key' => 'value']);

            $this->assertStringContainsString(
                '"context": "", "level": "info", "message": "Test message", "data": "{"key":"value"}"',
                $result
            );
        }
    }
}
