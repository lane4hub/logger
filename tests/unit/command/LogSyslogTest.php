<?php

namespace Jardis\Logger\Tests\unit\command;

use Jardis\Logger\command\LogSyslog;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class LogSyslogTest extends TestCase
{
    public function testSyslogNoMocks(): void
    {
        $logger = new LogSyslog(LogLevel::INFO);

        $result = $logger(LogLevel::INFO, 'TestMessage', ['key' => 'value']);

        $this->assertStringContainsString(
            '"context": "", "level": "info", "message": "TestMessage", "data": "{"key":"value"}"',
            $result
        );
    }
}
