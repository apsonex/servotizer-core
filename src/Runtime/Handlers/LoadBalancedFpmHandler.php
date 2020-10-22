<?php

namespace Apsonex\ServotizerCore\Runtime\Handlers;

use Apsonex\ServotizerCore\Runtime\Fpm\LoadBalancedFpmLambdaResponse;

class LoadBalancedFpmHandler extends FpmHandler
{
    /**
     * Covert FPM response to Lambda-ready response.
     *
     * @param  \Apsonex\ServotizerCore\Runtime\Fpm\FpmResponse  $fpmResponse
     * @return \Apsonex\ServotizerCore\Runtime\Fpm\FpmLambdaResponse
     */
    public function response($fpmResponse)
    {
        return new LoadBalancedFpmLambdaResponse(
            $fpmResponse->status,
            $fpmResponse->headers,
            $fpmResponse->body
        );
    }
}
