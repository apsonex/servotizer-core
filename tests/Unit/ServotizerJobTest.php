<?php

namespace Apsonex\ServotizerCore\Tests\Unit;

use Aws\Sqs\SqsClient;
use Illuminate\Container\Container;
use Apsonex\ServotizerCore\Queue\ServotizerConnector;
use Apsonex\ServotizerCore\Queue\ServotizerJob;
use Mockery;
use PHPUnit\Framework\TestCase;

class ServotizerJobTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function test_job_is_deleted_on_release_and_new_job_is_created()
    {
        $sqs = Mockery::mock(SqsClient::class);

        $sqs->shouldReceive('deleteMessage')->once()->with([
            'QueueUrl' => 'test-servotizer-queue-url',
            'ReceiptHandle' => 'test-receipt-handle',
        ]);

        $sqs->shouldReceive('sendMessage')->once()->with([
            'QueueUrl' => 'test-servotizer-queue-url',
            'MessageBody' => json_encode(['attempts' => 2]),
            'DelaySeconds' => 0,
        ]);

        $job = new ServotizerJob(new Container, $sqs, [
            'ReceiptHandle' => 'test-receipt-handle',
            'Body' => json_encode(['attempts' => 1]),
        ], 'sqs', 'test-servotizer-queue-url');

        $job->release();
    }

    public function test_can_determine_job_attempts()
    {
        $client = (new ServotizerConnector)->connect([
            'driver' => 'sqs',
            'key' => 'test-key',
            'secret' => 'test-secret',
            'prefix' => 'https://sqs.us-east-1.amazonaws.com/111111111',
            'queue' => 'test-queue',
            'region' => 'us-east-1',
        ]);

        $job = new ServotizerJob(new Container, $client->getSqs(), [
            'Body' => json_encode(['attempts' => 1]),
        ], 'sqs', 'test-servotizer-queue-url');

        $this->assertSame(2, $job->attempts());
    }

    public function test_handles_job_missing_attempts()
    {
        $client = (new ServotizerConnector)->connect([
            'driver' => 'sqs',
            'key' => 'test-key',
            'secret' => 'test-secret',
            'prefix' => 'https://sqs.us-east-1.amazonaws.com/111111111',
            'queue' => 'test-queue',
            'region' => 'us-east-1',
        ]);

        $job = new ServotizerJob(new Container, $client->getSqs(), [
            'Body' => json_encode([]),
        ], 'sqs', 'test-servotizer-queue-url');

        $this->assertSame(1, $job->attempts());
    }
}
