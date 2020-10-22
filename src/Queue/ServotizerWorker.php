<?php

namespace Apsonex\ServotizerCore\Queue;

use Illuminate\Queue\Worker;
use Illuminate\Queue\WorkerOptions;
use Apsonex\ServotizerCore\servotizerJobTimedOutException;

class ServotizerWorker extends Worker
{
    /**
     * Process the given job.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  string  $connectionName
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @return void
     */
    public function runservotizerJob($job, $connectionName, WorkerOptions $options)
    {
        pcntl_async_signals(true);

        pcntl_signal(SIGALRM, function () use ($job) {
            throw new servotizerJobTimedOutException($job->resolveName());
        });

        pcntl_alarm(
            max($this->timeoutForJob($job, $options), 0)
        );

        $this->runJob($job, $connectionName, $options);

        pcntl_alarm(0);
    }
}
