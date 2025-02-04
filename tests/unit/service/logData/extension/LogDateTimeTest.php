<?php

declare(strict_types=1);

namespace Jardis\Logger\Tests\unit\service\logData\extension;

use Jardis\Logger\service\logData\extension\LogDateTime;
use PHPUnit\Framework\TestCase;

class LogDateTimeTest extends TestCase
{
    /**
     * Verifiziert, dass die __invoke() Methode ein valides Datums- und Zeit-Format zurückgibt.
     */
    public function testInvokeReturnsValidDateTimeFormat(): void
    {
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', (new LogDateTime())());
    }

    /**
     * Verifiziert, dass die __invoke() Methode die aktuelle Zeit zurückgibt.
     */
    public function testInvokeReturnsCurrentDateTime(): void
    {
        $expected = (new \DateTime())->format('Y-m-d H:i:s');

        $this->assertTrue(abs(strtotime((new LogDateTime())()) - strtotime($expected)) <= 2);
    }
}
