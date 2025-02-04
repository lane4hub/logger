<?php

namespace Jardis\Logger\Tests\unit\command;

use Jardis\Logger\command\LogNull;
use Psr\Log\LogLevel;
use PHPUnit\Framework\TestCase;

class LogNullTest extends TestCase
{
    public function testWriteToStreamSuccess(): void
    {
        $logger = new LogNull(LogLevel::INFO);

        $result = $logger(LogLevel::INFO, 'Test message', ['key' => 'value']);

        $this->assertEquals(null, $result);
    }
}
