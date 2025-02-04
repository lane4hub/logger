<?php

namespace Jardis\Logger\Tests\unit;

use Jardis\Logger\command\LogCommandInterface;
use Jardis\Logger\command\LogConsole;
use Jardis\Logger\command\LogSlack;
use Jardis\Logger\Logger;
use Jardis\Logger\query\LogLevel;
use Jardis\Logger\service\history\LogHistory;
use Psr\Log\LogLevel as PsrLogLevel;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

class LoggerTest extends TestCase
{
    public function testNoActiveLoggers(): void
    {
        $logger = new Logger('');
        $logger->info('TestContext');
        $this->assertEmpty($logger->history());
    }

    /** @throws ReflectionException */
    public function testAddHandler(): void
    {
        $logger = new Logger('TestContext');

        $mockHandler = $this->createMock(LogCommandInterface::class);
        $logger->addLogger($mockHandler);

        $this->assertNotEmpty($this->getPrivateProperty($logger, 'logCommand'));
        $this->assertContains($mockHandler, $this->getPrivateProperty($logger, 'logCommand'));
    }

    public function testErrorViaHistory(): void
    {
        $logger = new Logger('TestContext', new LogHistory(5));
        $logger->addLogger(new LogSlack(PsrLogLevel::INFO, 'localhost'));

        $logger->info('Test message', ['key' => 'value']);

        $historyInfoLevel = $logger->history()->level(PsrLogLevel::INFO);
        $this->assertStringContainsString(
            'Failed to open stream',
            $historyInfoLevel['LogSlack'][0]
        );
    }

    public function testDebugMethod(): void
    {
        $logger = new Logger('TestContext',  new LogHistory(5));
        $consoleLogger = new LogConsole(PsrLogLevel::DEBUG);
        if ($mockStream = fopen('php://memory', 'r+')) {
            $consoleLogger->setStream($mockStream);
        }

        $logger->addLogger($consoleLogger);
        $logger->debug('Test debug message', ['key' => 'value']);

        $this->assertArrayHasKey('LogConsole', $logger->history()->level(PsrLogLevel::DEBUG));

    }

    public function testLogLevelMethods(): void
    {
        $logger = new Logger('TestContext', new LogHistory(10));
        $mockStream = fopen('php://memory', 'r+');

        foreach (LogLevel::COLLECTION as $level => $index) {
            $handler = new LogConsole($level);
            if ($mockStream) {
                $handler->setStream($mockStream);
            }
            $logger->addLogger($handler);

            $logger->{strtolower($level)}('Test message', ['key' => 'value']);
        }

        $this->assertCount(count(LogLevel::COLLECTION), $logger->history()->level());

        foreach ($logger->history()->level() as $log) {
            $this->arrayHasKey('LogConsole');
        }
    }

    /**
     * @return mixed
     * @throws ReflectionException
     */
    private function getPrivateProperty(object $object, string $propertyName)
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($object);
    }
}
