<?php

namespace Apsonex\ServotizerCore;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Apsonex\ServotizerCore\Queue\ServotizerWorker;

trait ConfiguresQueue
{
    /**
     * Ensure the queue / workers are configured.
     *
     * @return void
     */
    protected function ensureQueueIsConfigured()
    {
        if ($this->app->bound('queue.servotizerWorker')) {
            return;
        }

        $this->app->singleton('queue.servotizerWorker', function () {
            $isDownForMaintenance = function () {
                return $this->app->isDownForMaintenance();
            };

            return new ServotizerWorker(
                $this->app['queue'],
                $this->app['events'],
                $this->app[ExceptionHandler::class],
                $isDownForMaintenance
            );
        });
    }
}
