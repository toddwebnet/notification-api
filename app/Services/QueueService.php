<?php

namespace App\Services;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\DispatchesJobs;

class QueueService
{

    use DispatchesJobs, Queueable;

    const DEFAULT_QUEUE = 'sms';

    const AVAILABLE_QUEUES = [
        'sms',
        'email',
    ];

    /**
     * @return QueueService
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function instance()
    {
        return app()->make(self::class);
    }

    public function sendToQueue($class, $args = null, $queue = null, $runNow = false)
    {
        if ($queue == null) {
            $queue = self::DEFAULT_QUEUE;
        }

        $job = new $class($args);

        if ($runNow === true) {
            $this->dispatchNow($job->onQueue($queue));
        } else {
            $this->dispatch($job->onQueue($queue));
        }
    }

}
