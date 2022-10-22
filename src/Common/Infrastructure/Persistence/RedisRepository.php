<?php

namespace Jefero\Bot\Common\Infrastructure\Persistence;

use Redis;

class RedisRepository
{
    public const MINUTE_TIMEOUT = 60;
    public const FIVE_MINUTE_TIMEOUT = 300;
    public const HOUR_TIMEOUT = 3600;
    public const DAY_TIMEOUT = 86400;
    public const WEEK_TIMEOUT = 604800;
    public const MONTH_TIMEOUT = 2678400;

    private \Redis $client;
    private string $namespace;

    /**
     * RedisRepository constructor.
     * @param string $namespace
     * @param string $host
     * @param int $port
     * @param string|null $password
     */
    public function __construct(string $namespace, string $host, int $port, ?string $password = null)
    {
        $this->namespace = $namespace;
        $this->client = new \Redis();
        $this->client->connect($host, $port);
        if ($password) {
            $this->client->auth($password);
        }
    }

    /**
     * @param string $key
     * @param string|mixed $value
     * @param int $timeout
     * @return bool
     * @throws \JsonException
     * @throws \Exception
     */
    public function set(string $key, $value, int $timeout): bool
    {
        if (!self::isAvailableTimeout($timeout)) {
            throw new \Exception('Invalid redis timeout');
        }

        $encodedValue = json_encode($value, JSON_THROW_ON_ERROR);
        return $this->client->set(
            $this->addPrefix($key),
            $encodedValue,
            $timeout
        );
    }

    /**
     * @param string $key
     * @return false|mixed|string
     * @throws \JsonException
     */
    public function get(string $key)
    {
        $result = $this->client->get(
            $this->addPrefix($key)
        );
        return $result ? json_decode($result, true, 512, JSON_THROW_ON_ERROR) : null;
    }

    /**
     * https://redis.io/commands/getdel
     * @param string $key
     * @return mixed|null
     * @throws \JsonException
     */
    public function GETDEL(string $key)
    {
        $value = $this->client->rawCommand('GETDEL', $this->addPrefix($key));
        return is_bool($value) ? null : json_decode($value, true, 512, JSON_THROW_ON_ERROR);
    }

    private static function isAvailableTimeout(int $timeout): bool
    {
        return in_array(
            $timeout,
            [
                self::MINUTE_TIMEOUT,
                self::FIVE_MINUTE_TIMEOUT,
                self::HOUR_TIMEOUT,
                self::DAY_TIMEOUT,
                self::WEEK_TIMEOUT,
                self::MONTH_TIMEOUT
            ],
            true
        );
    }

    private function addPrefix(string $key): string
    {
        return $this->namespace . ':' . $key;
    }

    public function getRedis(): Redis
    {
        return $this->client;
    }
}
