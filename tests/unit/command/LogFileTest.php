<?php

namespace Jardis\Logger\Tests\unit\command;

use Exception;
use Jardis\Logger\command\LogFile;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class LogFileTest extends TestCase
{
    private string $filePath = __DIR__ . '/../../tmp/file.log';
    protected function tearDown(): void
    {
        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }
    }

    /** @throws Exception */
    public function testWritesToStream(): void
    {
        $logger = new LogFile(LogLevel::INFO, $this->filePath);

        $file = fopen($this->filePath, 'a');
        if ($file) {
            $logger->setStream($file);

            $logger(LogLevel::INFO, 'Message', ['key' => 'value']);

            $this->assertStringContainsString(
                '"context": "", "level": "info", "message": "Message", "data": "{"key":"value"}"',
                file_get_contents($this->filePath)
            );
        }
    }

    /** @throws Exception */
    public function testDirectoryNotExistsException(): void
    {
        $this->expectException(Exception::class);
        $logger = new LogFile(LogLevel::INFO, './no/path');
    }
}
