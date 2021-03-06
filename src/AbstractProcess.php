<?php

declare(strict_types=1);

namespace PeibinLaravel\Process;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use PeibinLaravel\Process\Contracts\Process as ProcessContract;
use PeibinLaravel\Process\Events\AfterProcessHandle;
use PeibinLaravel\Process\Events\BeforeProcessHandle;
use PeibinLaravel\Process\Events\PipeMessage;
use PeibinLaravel\Process\Exceptions\ServerInvalidException;
use PeibinLaravel\Process\Exceptions\SocketAcceptException;
use PeibinLaravel\Process\Utils\Constants;
use PeibinLaravel\Process\Utils\CoordinatorManager;
use PeibinLaravel\Utils\Contracts\Formatter;
use PeibinLaravel\Utils\Contracts\StdoutLogger;
use Swoole\Coroutine;
use Swoole\Coroutine\Channel;
use Swoole\Process as SwooleProcess;
use Swoole\Server;
use Swoole\Timer;
use Throwable;

abstract class AbstractProcess implements ProcessContract
{
    /**
     * Process name.
     *
     * @var string
     */
    public string $name = 'process';

    /**
     * Number of processes.
     *
     * @var int
     */
    public int $nums = 1;

    public int $pipeType = SOCK_DGRAM;

    public $enableCoroutine = true;

    protected ?SwooleProcess $process;

    protected ?Dispatcher $event;

    protected int $restartInterval = 5;

    protected int $recvLength = 65535;

    protected float $recvTimeout = 10.0;

    public function __construct(public Application $app)
    {
        if ($this->app->bound(Dispatcher::class)) {
            $this->event = $this->app->get(Dispatcher::class);
        }
    }

    /**
     * Create the process object according to process number and bind to server.
     *
     * @param Server $server
     */
    public function bind($server): void
    {
        if ($server instanceof Server) {
            $this->bindServer($server);
            return;
        }

        throw new ServerInvalidException(sprintf('Server %s is invalid.', get_class($server)));
    }

    /**
     * Determine if the process should start?
     *
     * @param Server $server
     */
    public function isEnable($server): bool
    {
        return true;
    }

    public function bindServer(Server $server)
    {
        $num = $this->nums;
        for ($i = 0; $i < $num; ++$i) {
            $process = new SwooleProcess(function (SwooleProcess $process) use ($i) {
                try {
                    $this->event && $this->event->dispatch(new BeforeProcessHandle($this, $i));

                    $this->process = $process;
                    if ($this->enableCoroutine) {
                        $quit = new Channel(1);
                        $this->listen($quit);
                    }

                    $this->handle();
                } catch (Throwable $throwable) {
                    $this->logThrowable($throwable);
                } finally {
                    $this->event && $this->event->dispatch(new AfterProcessHandle($this, $i));

                    isset($quit) && $quit->push(true);

                    Timer::clearAll();
                    CoordinatorManager::until(Constants::WORKER_EXIT)->resume();
                    sleep($this->restartInterval);
                }
            }, false, SOCK_DGRAM, $this->enableCoroutine);
            $server->addProcess($process);

            if ($this->enableCoroutine) {
                ProcessCollector::add($this->name, $process);
            }
        }
    }

    /**
     * Added event for listening data from worker/task.
     *
     * @param Channel $quit
     * @return void
     */
    protected function listen(Channel $quit): void
    {
        Coroutine::create(function () use ($quit) {
            while ($quit->pop(0.001) !== true) {
                try {
                    /** @var Coroutine\Socket $sock */
                    $sock = $this->process->exportSocket();
                    $recv = $sock->recv($this->recvLength, $this->recvTimeout);

                    if ($recv === '') {
                        throw new SocketAcceptException('Socket is closed', $sock->errCode);
                    }

                    if ($recv === false && $sock->errCode !== SOCKET_ETIMEDOUT) {
                        throw new SocketAcceptException('Socket is closed', $sock->errCode);
                    }

                    if ($this->event && is_string($recv) && $data = unserialize($recv)) {
                        $this->event->dispatch(new PipeMessage($data));
                    }
                } catch (Throwable $exception) {
                    $this->logThrowable($exception);
                    if ($exception instanceof SocketAcceptException) {
                        // TODO: Reconnect the socket.
                        break;
                    }
                }
            }
            $quit->close();
        });
    }

    protected function logThrowable(Throwable $throwable): void
    {
        if (app()->has(StdoutLogger::class) && app()->has(Formatter::class)) {
            $logger = app(StdoutLogger::class);
            $formatter = app(Formatter::class);
            $logger->error($formatter->format($throwable));

            if ($throwable instanceof SocketAcceptException) {
                $logger->critical('Socket of process is unavailable, please restart the server');
            }
        }
    }
}
