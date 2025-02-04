<?php

namespace Jardis\Logger\Tests\unit\command;

use Jardis\Logger\command\LogSlack;
use Psr\Log\LogLevel;
use PHPUnit\Framework\TestCase;
use Exception;

class LogSlackTest extends TestCase
{
    /** @throws Exception */
    public function testWriteToStreamMockStream(): void
    {
        $logger = new LogSlack(LogLevel::INFO, 'localhost');

        if ($stream = fopen('php://memory', 'r+')) {
            $logger->setStream($stream);
        }

        $result = $logger(LogLevel::INFO, 'Message', ['key' => 'value']);

        $this->assertStringContainsString(
            '"context":"","level":"info","message":"Message","data":{"key":"value"}',
            $result
        );
    }
}
