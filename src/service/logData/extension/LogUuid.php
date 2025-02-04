<?php

declare(strict_types=1);

namespace Jardis\Logger\service\logData\extension;

/**
 * Class LogUuid
 *
 * This class implements the LogDataInterface and provides a mechanism to
 * generate a universally unique identifier (UUID). When invoked, it returns
 * an array containing the generated UUID.
 */
class LogUuid implements LogExtensionInterface
{
    /**
     * Generates a random UUID v4 and returns it within an associative array.
     *
     * @return string An associative array containing the generated UUID with the key 'Uuid'.
     */
    public function __invoke(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}
