<?php

namespace Apsonex\ServotizerCore\Runtime\Handlers;

use Apsonex\ServotizerCore\Runtime\Http\LoadBalancedPsrRequestFactory;
use Apsonex\ServotizerCore\Runtime\LoadBalancedPsrLambdaResponseFactory;
use Psr\Http\Message\ResponseInterface;

class LoadBalancedAppHandler extends AppHandler
{
    /**
     * Create a new PSR-7 compliant request from the incoming event.
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    protected function marshalRequest(array $event)
    {
        return (new LoadBalancedPsrRequestFactory($event))->__invoke();
    }

    /**
     * Marshal the PSR-7 response to a Lambda response.
     *
     * @param  \Psr\Http\Message\ResponseInterface  $response
     * @return \Apsonex\ServotizerCore\Runtime\ArrayLambdaResponse
     */
    protected function marshalResponse(ResponseInterface $response)
    {
        return LoadBalancedPsrLambdaResponseFactory::fromPsrResponse($response);
    }
}
