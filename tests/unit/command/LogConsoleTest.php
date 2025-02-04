<?php

namespace Jardis\Logger\Tests\unit\command;

use Jardis\Logger\command\LogConsole;
use Jardis\Logger\service\logData\LogData;
use Psr\Log\LogLevel;
use PHPUnit\Framework\TestCase;

class LogConsoleTest extends TestCase
{
    public function testWriteToStreamSuccess(): void
    {
        $logger = new LogConsole(LogLevel::INFO);

        if ($mockStream = fopen('php://memory', 'r+')) {
            $logger->setStream($mockStream);
        }

        $result = $logger(LogLevel::INFO, 'message', ['key' => 'value']);

        $this->assertStringContainsString(
            '"context": "", "level": "info", "message": "message", "data": "{"key":"value"}"',
            $result
        );
    }

    public function testWriteContextToStreamSuccess(): void
    {
        $logger = new LogConsole(LogLevel::INFO);

        if ($mockStream = fopen('php://memory', 'r+')) {
            $logger->setStream($mockStream);
        }
        $logger->setContext('TestContext');
        $result = $logger(LogLevel::INFO, 'message', ['key' => 'value']);

        $this->assertStringContainsString(
            '"context": "TestContext", "level": "info", "message": "message", "data": "{"key":"value"}"',
            $result
        );
    }
    public function testNotWriteLevel(): void
    {
        $logger = new LogConsole(LogLevel::ERROR);

        if ($mockStream = fopen('php://memory', 'r+')) {
            $logger->setStream($mockStream);
        }

        $result = $logger(LogLevel::WARNING, 'message', ['key' => 'value']);

        $this->assertEquals('', $result);
    }

    public function testSetLogData(): void
    {
        $logger = new LogConsole(LogLevel::ERROR);
        $logger->setLogData(new LogData());

        if ($mockStream = fopen('php://memory', 'r+')) {
            $logger->setStream($mockStream);
        }

        $result = $logger(LogLevel::WARNING, 'message', ['key' => 'value']);

        $this->assertEquals('', $result);
    }

    public function testLogFalse(): void
    {
        $logger = new LogConsole(LogLevel::WARNING);
        $logger->setStream();

        $result = $logger(LogLevel::WARNING, 'message', ['key' => 'value']);

        $this->assertEquals('', $result);
    }
}
