<?php

declare(strict_types=1);

namespace PeibinLaravel\Process;

use Illuminate\Support\ServiceProvider;
use Laravel\Octane\Events\MainServerStarting;
use PeibinLaravel\Process\Events\AfterProcessHandle;
use PeibinLaravel\Process\Events\BeforeProcessHandle;
use PeibinLaravel\Process\Listeners\BootProcessListener;
use PeibinLaravel\Process\Listeners\InitProcessTitleListener;
use PeibinLaravel\Process\Listeners\LogAfterProcessStoppedListener;
use PeibinLaravel\Process\Listeners\LogBeforeProcessStartListener;
use PeibinLaravel\Utils\Contracts\StdoutLogger as StdoutLoggerContract;
use PeibinLaravel\Utils\Providers\RegisterProviderConfig;
use PeibinLaravel\Utils\StdoutLogger;

class ProcessServiceProvider extends ServiceProvider
{
    use RegisterProviderConfig;

    public function __invoke(): array
    {
        return [
            'dependencies' => [
                StdoutLoggerContract::class => StdoutLogger::class,
            ],
            'listeners'    => [
                MainServerStarting::class  => BootProcessListener::class,
                BeforeProcessHandle::class => [
                    InitProcessTitleListener::class,
                    LogBeforeProcessStartListener::class,
                ],
                AfterProcessHandle::class  => [
                    LogAfterProcessStoppedListener::class,
                ],
            ],
        ];
    }
}
