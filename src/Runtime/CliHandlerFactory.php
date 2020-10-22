<?php

namespace Apsonex\ServotizerCore\Runtime;

use Apsonex\ServotizerCore\Runtime\Handlers\CliHandler;
use Apsonex\ServotizerCore\Runtime\Handlers\QueueHandler;

class CliHandlerFactory
{
    /**
     * Create a new handler for the given CLI event.
     *
     * @param array $event
     * @return mixed
     */
    public static function make(array $event)
    {
        return isset($event['Records'][0]['messageId']) ? new QueueHandler : new CliHandler;
    }
}
