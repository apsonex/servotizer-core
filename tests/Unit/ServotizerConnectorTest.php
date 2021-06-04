<?php

namespace Apsonex\ServotizerCore\Tests\Unit;

use Apsonex\ServotizerCore\Queue\ServotizerConnector;
use Apsonex\ServotizerCore\Queue\ServotizerQueue;
use PHPUnit\Framework\TestCase;

class ServotizerConnectorTest extends TestCase
{
    public function test_can_create_servotizer_queue()
    {
        $queue = (new ServotizerConnector)->connect([
            'driver' => 'sqs',
            'key' => 'test-key',
            'secret' => 'test-secret',
            'prefix' => 'https://sqs.us-east-1.amazonaws.com/111111111',
            'queue' => 'test-queue',
            'region' => 'us-east-1',
        ]);

        $this->assertInstanceOf(ServotizerQueue::class, $queue);
    }
}
