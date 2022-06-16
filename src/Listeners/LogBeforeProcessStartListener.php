<?php

declare(strict_types=1);

namespace PeibinLaravel\Process\Listeners;

use PeibinLaravel\Process\Events\BeforeProcessHandle;
use PeibinLaravel\Utils\Contracts\StdoutLogger;

class LogBeforeProcessStartListener
{
    public function handle(object $event): void
    {
        if ($event instanceof BeforeProcessHandle) {
            $message = sprintf('Process[%s.%d] start.', $event->process->name, $event->index);
            if (app()->has(StdoutLogger::class)) {
                app(StdoutLogger::class)->info($message);
            } else {
                echo $message . PHP_EOL;
            }
        }
    }
}
