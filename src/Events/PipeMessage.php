<?php

namespace PeibinLaravel\Process\Events;

class PipeMessage
{
    public function __construct(public mixed $data)
    {
    }
}
