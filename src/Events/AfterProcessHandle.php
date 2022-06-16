<?php

declare(strict_types=1);

namespace PeibinLaravel\Process\Events;

use PeibinLaravel\Process\AbstractProcess;

class AfterProcessHandle
{
    public function __construct(public AbstractProcess $process, public int $index)
    {
    }
}
