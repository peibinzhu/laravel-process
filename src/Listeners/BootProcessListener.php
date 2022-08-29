<?php

declare(strict_types=1);

namespace PeibinLaravel\Process\Listeners;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\Container;
use PeibinLaravel\Di\Annotation\AnnotationCollector;
use PeibinLaravel\Process\Annotations\Process;
use PeibinLaravel\Process\Contracts\Process as ProcessContract;
use PeibinLaravel\Process\ProcessManager;
use PeibinLaravel\SwooleEvent\Events\BeforeMainServerStart;

class BootProcessListener
{
    public function __construct(private Container $container, private Repository $config)
    {
    }

    public function handle(object $event): void
    {
        if ($event instanceof BeforeMainServerStart) {
            $server = $event->server;
            $serverConfig = $event->serverConfig;

            $serverProcesses = $serverConfig['processes'] ?? [];
            $processes = $this->config->get('processes', []);
            $annotationProcesses = $this->getAnnotationProcesses();

            // Retrieve the processes have been registered.
            $processes = array_merge(
                $serverProcesses,
                $processes,
                ProcessManager::all(),
                array_keys($annotationProcesses)
            );
            foreach ($processes as $process) {
                if (is_string($process)) {
                    $instance = $this->container->make($process);
                } else {
                    $instance = $process;
                }
                if ($instance instanceof ProcessContract) {
                    $instance->isEnable($server) && $instance->bind($server);
                }
            }
        }
    }

    private function getAnnotationProcesses(): array
    {
        return AnnotationCollector::getClassesByAnnotation(Process::class);
    }
}
