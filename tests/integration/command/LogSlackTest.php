<?php

namespace Jardis\Logger\Tests\integration\command;

use Jardis\Logger\command\LogSlack;
use Psr\Log\LogLevel;
use PHPUnit\Framework\TestCase;
use Exception;

class LogSlackTest extends TestCase
{
    /** @throws Exception */
    public function testWriteToStreamMockStream(): void
    {
        $logger = new LogSlack(LogLevel::INFO, $_ENV['SLACK_WEBHOOK_URL']);

        $result = $logger(LogLevel::INFO, 'Test SlackMessage', ['Test' => 'test']);

        $this->assertStringContainsString('SlackMessage', $result ?? '');
    }
}
