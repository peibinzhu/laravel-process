<?php

declare(strict_types=1);

namespace PeibinLaravel\Process\Contracts;

use Swoole\Server;

interface ProcessInterface
{
    /**
     * Create the process object according to process number and bind to server.
     *
     * @param Server $server
     */
    public function bind($server): void;

    /**
     * Determine if the process should start?
     *
     * @param Server $server
     */
    public function isEnable($server): bool;

    /**
     * The logical of process will place in here.
     *
     */
    public function handle(): void;
}
