<?php

declare(strict_types=1);

namespace Jardis\Logger\Tests\unit\service\logData;

use Jardis\Logger\service\logData\LogData;
use PHPUnit\Framework\TestCase;

class LogDataTest extends TestCase
{
    public function testConstructorInitializesDefaults(): void
    {
        $logData = new LogData();

        $expectedRecordLogData = [
            LogData::CONTEXT => '',
            LogData::LEVEL => '',
            LogData::MESSAGE => '',
        ];

        $this->assertSame($expectedRecordLogData, $this->getPrivateProperty($logData, 'recordLogData'));
        $this->assertSame([], $this->getPrivateProperty($logData, 'additionalUserLogData'));
    }

    public function testConstructorInitializesWithCustomValues(): void
    {
        $additionalRecordLogData = ['customKey' => 'customValue'];
        $additionalUserLogData = ['userKey' => 'userValue'];

        $logData = new LogData($additionalRecordLogData, $additionalUserLogData);

        $expectedRecordLogData = [
            'customKey' => 'customValue',
            LogData::CONTEXT => '',
            LogData::LEVEL => '',
            LogData::MESSAGE => '',
        ];

        $this->assertSame($expectedRecordLogData, $this->getPrivateProperty($logData, 'recordLogData'));
        $this->assertSame($additionalUserLogData, $this->getPrivateProperty($logData, 'additionalUserLogData'));
    }

    public function testInvokeGeneratesLogDataWithCorrectValues(): void
    {
        $logData = new LogData();

        $result = $logData('testContext', 'info', 'Hello {name} mit {json}!', ['name' => 'World', 'json' => ['content']]);

        $this->assertSame('testContext', $result[LogData::CONTEXT]);
        $this->assertSame('info', $result[LogData::LEVEL]);
        $this->assertSame('Hello World mit ["content"]!', $result[LogData::MESSAGE]);
    }

    public function testAddRecordLogDataAddsNewField(): void
    {
        $logData = new LogData();
        $logData->addLogData('newField', fn() => 'computedValue');

        $recordLogData = $this->getPrivateProperty($logData, 'recordLogData');
        $this->assertTrue(array_key_exists('newField', $recordLogData));
        $this->assertEquals('computedValue', $recordLogData['newField']());
    }

    public function testAddRecordLogDataDoesNotOverwriteExistingField(): void
    {
        $logData = new LogData();
        $logData->addLogData('existingField', fn() => 'initialValue');
        $logData->addLogData('existingField', fn() => 'newValue');

        $recordLogData = $this->getPrivateProperty($logData, 'recordLogData');
        $this->assertEquals('initialValue', $recordLogData['existingField']());
    }

    public function testAddUserLogDataAddsNewField(): void
    {
        $logData = new LogData();
        $logData->addUserLogData('userField', fn() => 'userValue');

        $userLogData = $this->getPrivateProperty($logData, 'additionalUserLogData');
        $this->assertTrue(array_key_exists('userField', $userLogData));
        $this->assertEquals('userValue', $userLogData['userField']());
    }

    public function testAddUserLogDataDoesNotOverwriteExistingField(): void
    {
        $logData = new LogData();
        $logData->addUserLogData('userField', fn() => 'initialValue');
        $logData->addUserLogData('userField', fn() => 'newValue');

        $userLogData = $this->getPrivateProperty($logData, 'additionalUserLogData');
        $this->assertEquals('initialValue', $userLogData['userField']());
    }

    public function testInterpolateReplacesPlaceholdersCorrectly(): void
    {
        $logData = new LogData();
        $result = $this->callPrivateMethod($logData, 'interpolate', ['{callable} hello {name}!', ['name' => 'world', 'callable' => fn() => 'Call']]);

        $this->assertSame('Call hello world!', $result);
    }

    public function testInterpolateDoesNotReplaceMissingPlaceholders(): void
    {
        $logData = new LogData();
        $result = $this->callPrivateMethod($logData, 'interpolate', ['Hello {name}!', []]);

        $this->assertSame('Hello {name}!', $result);
    }

    public function testInterpolateLeavesMessagesWithoutPlaceholdersUnchanged(): void
    {
        $logData = new LogData();
        $result = $this->callPrivateMethod($logData, 'interpolate', ['Hello World!', []]);

        $this->assertSame('Hello World!', $result);
    }

    /**
     * Helper method to access private properties for testing.
     */
    private function getPrivateProperty(object $object, string $property)
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($object);
    }

    /**
     * Helper method to call private methods for testing.
     */
    private function callPrivateMethod(object $object, string $method, array $parameters = [])
    {
        $reflection = new \ReflectionClass($object);
        $method = $reflection->getMethod($method);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
