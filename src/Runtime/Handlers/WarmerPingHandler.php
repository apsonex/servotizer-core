<?php

namespace Apsonex\ServotizerCore\Runtime\Handlers;

use Apsonex\ServotizerCore\Contracts\LambdaEventHandler;
use Apsonex\ServotizerCore\Runtime\ArrayLambdaResponse;

class WarmerPingHandler implements LambdaEventHandler
{
    /**
     * Handle an incoming Lambda event.
     *
     * @param  array  $event
     * @param  \Apsonex\ServotizerCore\Contracts\LambdaResponse
     */
    public function handle(array $event)
    {
        usleep(50 * 1000);

        return new ArrayLambdaResponse([
            'output' => 'Warmer ping handled.',
        ]);
    }
}
