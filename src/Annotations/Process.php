<?php

declare(strict_types=1);

namespace PeibinLaravel\Process\Annotations;

use Attribute;
use PeibinLaravel\Di\Annotation\AbstractAnnotation;

#[Attribute(Attribute::TARGET_CLASS)]
class Process extends AbstractAnnotation
{
    public function __construct(
        public ?int $nums = null,
        public ?string $name = null,
        public ?bool $redirectStdinStdout = null,
        public ?int $pipeType = null,
        public ?bool $enableCoroutine = null
    ) {
    }
}
