<?php

declare(strict_types=1);

namespace PeibinLaravel\Process;

use Illuminate\Support\ServiceProvider;
use PeibinLaravel\Process\Events\AfterProcessHandle;
use PeibinLaravel\Process\Events\BeforeProcessHandle;
use PeibinLaravel\Process\Listeners\BootProcessListener;
use PeibinLaravel\Process\Listeners\LogAfterProcessStoppedListener;
use PeibinLaravel\Process\Listeners\LogBeforeProcessStartListener;
use PeibinLaravel\ProviderConfig\Contracts\ProviderConfigInterface;
use PeibinLaravel\SwooleEvent\Events\BeforeMainServerStart;

class ProcessServiceProvider extends ServiceProvider implements ProviderConfigInterface
{
    public function __invoke(): array
    {
        return [
            'listeners' => [
                BeforeMainServerStart::class => [
                    BootProcessListener::class,
                ],
                BeforeProcessHandle::class   => [
                    LogBeforeProcessStartListener::class,
                ],
                AfterProcessHandle::class    => [
                    LogAfterProcessStoppedListener::class,
                ],
            ],
        ];
    }
}
