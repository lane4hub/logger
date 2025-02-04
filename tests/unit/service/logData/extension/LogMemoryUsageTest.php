<?php

declare(strict_types=1);

namespace Jardis\Logger\Tests\unit\service\logData\extension;

use Jardis\Logger\service\logData\extension\LogMemoryUsage;
use PHPUnit\Framework\TestCase;

class LogMemoryUsageTest extends TestCase
{
    /**
     * Testet, ob die __invoke-Methode einen korrekt formatierten String zurückgibt.
     */
    public function testInvokeReturnsFormattedMemoryUsage()
    {
        $result = (new LogMemoryUsage())();

        $this->assertMatchesRegularExpression(
            '/^[0-9]+\.[0-9]{2} MB\*\* \([0-9]+ Bytes\)\.$/',
            $result,
        );
    }
}
