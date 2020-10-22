<?php

namespace Apsonex\ServotizerCore\Runtime;

use Apsonex\ServotizerCore\Runtime\Handlers\FpmHandler;
use Apsonex\ServotizerCore\Runtime\Handlers\LoadBalancedFpmHandler;
use Apsonex\ServotizerCore\Runtime\Handlers\UnknownEventHandler;
use Apsonex\ServotizerCore\Runtime\Handlers\WarmerHandler;
use Apsonex\ServotizerCore\Runtime\Handlers\WarmerPingHandler;

class HttpHandlerFactory
{
    /**
     * Create a new handler for the given HTTP event.
     *
     * @param  array  $event
     * @return \Apsonex\ServotizerCore\Contracts\LambdaEventHandler
     */
    public static function make(array $event)
    {
        if (isset($event['servotizerWarmer'])) {
            return new WarmerHandler;
        } elseif (isset($event['servotizerWarmerPing'])) {
            return new WarmerPingHandler;
        } elseif (isset($event['requestContext']['elb'])) {
            return new LoadBalancedFpmHandler;
//            return new LoadBalancedAppHandler;
        } elseif (isset($event['httpMethod'])) {
            return new FpmHandler;
        // return new AppHandler;
        } else {
            return new UnknownEventHandler;
        }
    }
}
