<?php

namespace Apsonex\ServotizerCore\Runtime\Handlers;

use Apsonex\ServotizerCore\Contracts\LambdaEventHandler;
use Apsonex\ServotizerCore\Runtime\ArrayLambdaResponse;
use Apsonex\ServotizerCore\Runtime\Logger;

class UnknownEventHandler implements LambdaEventHandler
{
    /**
     * Handle an incoming Lambda event.
     *
     * @param  array  $event
     * @param  \Apsonex\ServotizerCore\Contracts\LambdaResponse
     */
    public function handle(array $event)
    {
        Logger::info('Unknown event type received by application.', [
            'event' => $event,
        ]);

        return new ArrayLambdaResponse([
            'output' => 'Unknown event type.',
        ]);
    }
}
