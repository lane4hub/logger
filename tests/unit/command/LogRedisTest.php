<?php

declare(strict_types=1);

namespace Jardis\Logger\Tests\unit\command;

use Jardis\Logger\command\LogRedis;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Redis;
use RedisException;

class LogRedisTest extends TestCase
{
    private $mockRedis;

    protected function setUp(): void
    {
        $this->mockRedis = $this->createMock(Redis::class);
    }

    public function testLogSuccess(): void
    {
        $this->mockRedis->expects($this->any())
            ->method('setex')
            ->with(
                $this->isType('string'),
                3600,
                $this->isType('string')
            )
            ->willReturn(true);

        $logRedis = new LogRedis(LogLevel::INFO, $this->mockRedis);

        $result = $logRedis(LogLevel::INFO, 'This is a test message', ['key' => 'value']);

        $this->assertStringContainsString(
            '{ "context": "", "level": "info", "message": "This is a test message", "data": "{"key":"value"}" }',
            $result);
    }

    public function testLogFailureDueToException(): void
    {
        $this->mockRedis->expects($this->once())
            ->method('setex')
            ->willThrowException(new RedisException('Redis error'));

        $this->expectException(RedisException::class);

        $logRedis = new LogRedis(LogLevel::ERROR, $this->mockRedis, 5);

        $logRedis(LogLevel::ERROR, 'Error in test log',  ['errorKey' => 'errorValue']);
    }

    public function testEncodeJsonSuccess(): void
    {
        $logRedis = new class('info', $this->mockRedis) extends LogRedis {
            public function testEncode($value): string
            {
                return $this->encode($value);
            }
        };

        $data = ['key' => 'value'];
        $expectedJson = json_encode($data);

        $this->assertSame(
            $expectedJson,
            $logRedis->testEncode($data),
            'Encode should correctly return JSON string for an array.'
        );
    }

    public function testEncodeFallbackToSerialization(): void
    {
        $logRedis = new class('info', $this->mockRedis) extends LogRedis {
            public function testEncode($value): string
            {
                return $this->encode($value);
            }
        };

        // Simulate invalid JSON by encoding a resource
        $data = fopen('php://memory', 'r'); // resources cannot be JSON-encoded

        $result = $logRedis->testEncode($data);

        $this->assertStringContainsString(
            'i:0',
            $result,
            'Encode should fall back to serialization when JSON encoding fails.'
        );
    }
}
