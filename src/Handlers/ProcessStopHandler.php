<?php

declare(strict_types=1);

namespace PeibinLaravel\Process\Handlers;

use PeibinLaravel\Process\ProcessManager;
use PeibinLaravel\Signal\SignalHandlerInterface;

class ProcessStopHandler implements SignalHandlerInterface
{
    public function listen(): array
    {
        return [
            [self::PROCESS, SIGTERM],
        ];
    }

    public function handle(int $signal): void
    {
        ProcessManager::setRunning(false);
    }
}
