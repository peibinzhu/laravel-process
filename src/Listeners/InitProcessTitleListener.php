<?php

declare(strict_types=1);

namespace PeibinLaravel\Process\Listeners;

use Laravel\Octane\Swoole\SwooleExtension;
use PeibinLaravel\Process\Events\BeforeProcessHandle;

class InitProcessTitleListener
{
    private string $dot = '.';

    public function handle(object $event): void
    {
        if ($event instanceof BeforeProcessHandle) {
            $appName = config('app.name', 'Laravel');
            $processName = 'user process ' . implode($this->dot, [$event->process->name, $event->index]);
            (new SwooleExtension())->setProcessName($appName, $processName);
        }
    }
}
