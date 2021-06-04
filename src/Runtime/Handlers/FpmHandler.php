<?php

namespace Apsonex\ServotizerCore\Runtime\Handlers;

use Apsonex\ServotizerCore\Contracts\LambdaEventHandler;
use Apsonex\ServotizerCore\Runtime\Fpm\Fpm;
use Apsonex\ServotizerCore\Runtime\Fpm\FpmLambdaResponse;
use Apsonex\ServotizerCore\Runtime\Fpm\FpmRequest;

class FpmHandler implements LambdaEventHandler
{
    /**
     * Handle an incoming Lambda event.
     *
     * @param  array  $event
     * @return  \Apsonex\ServotizerCore\Contracts\LambdaResponse
     */
    public function handle(array $event)
    {
        return $this->response(
            Fpm::resolve()->handle($this->request($event))
        );
    }

    /**
     * Create a new fpm request from the incoming event.
     *
     * @param  array  $event
     * @return \Apsonex\ServotizerCore\Runtime\Fpm\FpmRequest
     */
    public function request($event)
    {
        return FpmRequest::fromLambdaEvent(
            $event, Fpm::resolve()->handler(), $this->serverVariables()
        );
    }

    /**
     * Covert FPM response to Lambda-ready response.
     *
     * @param  \Apsonex\ServotizerCore\Runtime\Fpm\FpmResponse  $fpmResponse
     * @return \Apsonex\ServotizerCore\Runtime\Fpm\FpmLambdaResponse
     */
    public function response($fpmResponse)
    {
        return new FpmLambdaResponse(
            $fpmResponse->status,
            $fpmResponse->headers,
            $fpmResponse->body
        );
    }

    /**
     * Get the server variables.
     *
     * @return array
     */
    public function serverVariables()
    {
        return array_merge(Fpm::resolve()->serverVariables(), array_filter([
            'AWS_REQUEST_ID' => $_ENV['AWS_REQUEST_ID'] ?? null,
        ]));
    }
}
