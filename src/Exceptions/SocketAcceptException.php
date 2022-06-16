<?php

declare(strict_types=1);

namespace PeibinLaravel\Process\Exceptions;

use RuntimeException;

class SocketAcceptException extends RuntimeException
{
    public function isTimeout(): bool
    {
        return $this->getCode() === SOCKET_ETIMEDOUT;
    }
}
