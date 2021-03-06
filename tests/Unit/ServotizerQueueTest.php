<?php

namespace Apsonex\ServotizerCore\Tests\Unit;

use Aws\Sqs\SqsClient;
use Apsonex\ServotizerCore\Queue\ServotizerQueue;
use Mockery;
use PHPUnit\Framework\TestCase;

class ServotizerQueueTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function test_proper_payload_array_is_created()
    {
        $sqs = Mockery::mock(SqsClient::class);

        $job = new FakeJob;

        $sqs->shouldReceive('sendMessage')->once()->with(Mockery::on(function ($argument) use ($job) {
            $messageBody = json_decode($argument['MessageBody'], true);

            $this->assertSame('/test-servotizer-queue-url', $argument['QueueUrl']);
            $this->assertArraySubset([
                'displayName' => FakeJob::class,
                'job' => 'Illuminate\Queue\CallQueuedHandler@call',
                'maxTries' => null,
                'timeout' => null,
                'data' => [
                    'commandName' => FakeJob::class,
                    'command' => serialize($job),
                ],
                'attempts' => 0,
            ], $messageBody);

            return true;
        }))->andReturnSelf();

        $sqs->shouldReceive('get')->andReturn('attribute-value');

        $queue = new ServotizerQueue($sqs, 'test-servotizer-queue-url');

        $this->assertSame('attribute-value', $queue->push($job));
    }
}
