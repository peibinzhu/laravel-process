<?php

declare(strict_types=1);

namespace PeibinLaravel\Process;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use PeibinLaravel\Process\Events\AfterProcessHandle;
use PeibinLaravel\Process\Events\BeforeProcessHandle;
use PeibinLaravel\Process\Listeners\BootProcessListener;
use PeibinLaravel\Process\Listeners\LogAfterProcessStoppedListener;
use PeibinLaravel\Process\Listeners\LogBeforeProcessStartListener;
use PeibinLaravel\SwooleEvent\Events\BeforeMainServerStart;

class ProcessServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $listeners = [
            BeforeMainServerStart::class => [
                BootProcessListener::class,
            ],
            BeforeProcessHandle::class   => [
                LogBeforeProcessStartListener::class,
            ],
            AfterProcessHandle::class    => [
                LogAfterProcessStoppedListener::class,
            ],
        ];
        $this->registerListeners($listeners);
    }

    private function registerListeners(array $listeners)
    {
        $dispatcher = $this->app->get(Dispatcher::class);
        foreach ($listeners as $event => $_listeners) {
            foreach ((array)$_listeners as $listener) {
                $dispatcher->listen($event, $listener);
            }
        }
    }
}
