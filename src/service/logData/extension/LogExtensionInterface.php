<?php

declare(strict_types=1);

namespace Jardis\Logger\service\logData\extension;

interface LogExtensionInterface
{
    /**
     * Invokes the object as a function.
     *
     * @return string|array<string, string> Returns an array or string data upon invocation.
     */
    public function __invoke();
}
