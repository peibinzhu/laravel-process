<?php

declare(strict_types=1);

namespace PeibinLaravel\Process\Listeners;

use Illuminate\Config\Repository;
use Laravel\Octane\Swoole\SwooleExtension;
use PeibinLaravel\Process\Events\BeforeProcessHandle;

class InitProcessTitleListener
{
    private string $dot = '.';

    public function __construct(private Repository $config)
    {
    }

    public function handle(object $event): void
    {
        if ($event instanceof BeforeProcessHandle) {
            $appName = $this->config->get('app.name', 'Laravel');
            $processName = 'user process ' . implode($this->dot, [$event->process->name, $event->index]);
            (new SwooleExtension())->setProcessName($appName, $processName);
        }
    }
}
