<?php

declare(strict_types=1);

namespace Jardis\Logger\command;

use Redis;
use RedisException;

/**
 * Class LogRedis
 *
 * This class extends LogCommand and provides functionality to log data into
 * a Redis store. It supports time-to-live (TTL) for the log entries to define their expiration period.
 */
class LogRedis extends LogCommand
{
    private Redis $redis;
    private int $ttl;

    /**
     * Constructor method to initialize the class with required dependencies.
     *
     * @param string $logLevel The log level to be used for the logger.
     * @param Redis $redis An instance of the Redis client.
     * @param int|null $ttl The time-to-live for cached entries in seconds. Defaults to 3600.
     *
     * @return void
     */
    public function __construct(string $logLevel, Redis $redis, ?int $ttl = 3600)
    {
        $this->redis = $redis;
        $this->ttl = $ttl ?? 3600;
        parent::__construct($logLevel);
    }

    protected function log(string $logMessage, array $logData): bool
    {
        return $this->redis->setex($this->hash(), $this->ttl, $this->encode($logData));
    }

    private function hash(): string
    {
        return 'Redis' . uniqid('', true);
    }

    /**
     * Encodes the given value to a string format using JSON encoding,
     * and falls back to serialization if encoding fails.
     *
     * @param mixed $value The value to be encoded.
     *
     * @return string The encoded string representation of the value.
     */
    protected function encode($value): string
    {
        $result = json_encode($value);
        if ($result === false || json_last_error() !== JSON_ERROR_NONE) {
            $result = serialize($value);
        }

        return $result;
    }
}
