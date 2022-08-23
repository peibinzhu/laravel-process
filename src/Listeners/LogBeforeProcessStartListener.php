<?php

declare(strict_types=1);

namespace PeibinLaravel\Process\Listeners;

use Illuminate\Contracts\Container\Container;
use PeibinLaravel\Process\Events\BeforeProcessHandle;
use PeibinLaravel\Utils\Contracts\StdoutLogger;

class LogBeforeProcessStartListener
{
    public function __construct(private Container $container)
    {
    }

    public function handle(object $event): void
    {
        if ($event instanceof BeforeProcessHandle) {
            $message = sprintf('Process[%s.%d] start.', $event->process->name, $event->index);
            if ($this->container->has(StdoutLogger::class)) {
                $this->container->get(StdoutLogger::class)->info($message);
            } else {
                echo $message . PHP_EOL;
            }
        }
    }
}
