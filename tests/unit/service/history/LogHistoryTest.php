<?php

declare(strict_types=1);

namespace Jardis\Logger\Tests\unit\service\history;

use Jardis\Logger\service\history\LogHistory;
use PHPUnit\Framework\TestCase;

class LogHistoryTest extends TestCase
{
    public function testAddMethodStoresResultsCorrectly(): void
    {
        $logHistory = new LogHistory();
        $logHistory->add('info', 'TestClass', 'result1');
        $logHistory->add('info', 'TestClass', 'result2');

        $expected = [
            'info' => [
                'TestClass' => ['result1', 'result2']
            ]
        ];

        $this->assertSame($expected, $logHistory->level());
    }

    public function testReduceMethodMaintainsMaxHistory(): void
    {
        $logHistory = new LogHistory(3);
        $logHistory->add('info', 'TestClass', 'result1');
        $logHistory->add('info', 'TestClass', 'result2');
        $logHistory->add('info', 'TestClass', 'result3');
        $logHistory->reduce('info', 'TestClass');
        $logHistory->add('info', 'TestClass', 'result4');
        $logHistory->reduce('info', 'TestClass');

        $expected = [
            'info' => [
                'TestClass' => ['result3', 'result4']
            ]
        ];

        $this->assertSame($expected, $logHistory->level());
    }

    public function testLevelMethodReturnsCorrectDataForSpecificLevel(): void
    {
        $logHistory = new LogHistory();
        $logHistory->add('info', 'TestClass', 'result1');
        $logHistory->add('error', 'ErrorClass', 'result2');

        $expectedInfoLevel = [
            'TestClass' => ['result1']
        ];

        $this->assertSame($expectedInfoLevel, $logHistory->level('info'));

        $expectedErrorLevel = [
            'ErrorClass' => ['result2']
        ];

        $this->assertSame($expectedErrorLevel, $logHistory->level('error'));
    }

    public function testLevelMethodReturnsEmptyArrayForUnknownLevel(): void
    {
        $logHistory = new LogHistory();
        $this->assertSame([], $logHistory->level('warning'));
    }

    public function testConstructorInitializesCorrectMaxHistory(): void
    {
        $logHistory = new LogHistory(5);
        $reflection = new \ReflectionClass($logHistory);
        $maxHistoryProperty = $reflection->getProperty('maxHistory');
        $maxHistoryProperty->setAccessible(true);

        $this->assertSame(5, $maxHistoryProperty->getValue($logHistory));
    }
}
