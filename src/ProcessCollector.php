<?php

declare(strict_types=1);

namespace PeibinLaravel\Process;

use Swoole\Process;

class ProcessCollector
{
    protected static array $processes = [];

    public static function add($name, Process $process): void
    {
        static::$processes[$name][] = $process;
    }

    public static function get($name): array
    {
        return static::$processes[$name] ?? [];
    }

    public static function all(): array
    {
        $result = [];
        foreach (static::$processes as $name => $processes) {
            $result = array_merge($result, $processes);
        }
        return $result;
    }

    public static function isEmpty(): bool
    {
        return static::$processes === [];
    }
}
