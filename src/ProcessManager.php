<?php

declare(strict_types=1);

namespace PeibinLaravel\Process;

use PeibinLaravel\Process\Contracts\ProcessInterface;

class ProcessManager
{
    protected static array $processes = [];

    /**
     * @var bool
     */
    protected static $running = true;

    public static function register(ProcessInterface $process): void
    {
        static::$processes[] = $process;
    }

    public static function all(): array
    {
        return static::$processes;
    }

    public static function clear(): void
    {
        static::$processes = [];
    }

    public static function isRunning(): bool
    {
        return static::$running;
    }

    public static function setRunning(bool $running): void
    {
        static::$running = $running;
    }
}
