<?php

declare(strict_types=1);

namespace PeibinLaravel\Process\Listeners;

use Laravel\Octane\Events\MainServerStarting;
use PeibinLaravel\Process\Contracts\Process;

class BootProcessListener
{
    public function handle(object $event): void
    {
        if ($event instanceof MainServerStarting) {
            $server = $event->server;
            $processes = config('processes', null);
            foreach ($processes as $process) {
                if (is_string($process)) {
                    $instance = app($process);
                } else {
                    $instance = $process;
                }
                if ($instance instanceof Process) {
                    $instance->isEnable($server) && $instance->bind($server);
                }
            }
        }
    }
}
