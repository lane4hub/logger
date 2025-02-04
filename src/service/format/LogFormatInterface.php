<?php

declare(strict_types=1);

namespace Jardis\Logger\service\format;

interface LogFormatInterface
{
    /**
     * @param array<string, mixed >$logData
     * @return string
     */
    public function __invoke(array $logData): string;
}
