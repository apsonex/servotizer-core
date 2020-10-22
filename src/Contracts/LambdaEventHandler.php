<?php

namespace Apsonex\ServotizerCore\Contracts;

interface LambdaEventHandler
{
    /**
     * Handle an incoming Lambda event.
     *
     * @param  array  $event
     * @param  \Apsonex\ServotizerCore\Contracts\LambdaResponse
     */
    public function handle(array $event);
}
