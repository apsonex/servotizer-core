<?php

namespace Apsonex\ServotizerCore\Tests\Unit;

use Mockery;
use Orchestra\Testbench\TestCase;

class ServotizerWorkCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function test_command_can_be_called()
    {
        $this->assertFalse(FakeJob::$handled);

        $job = new FakeJob;

        $message = base64_encode(json_encode([
            'messageId' => 'test-message-id',
            'receiptHandle' => 'test-receipt-handle',
            'body' => json_encode([
                'displayName' => FakeJob::class,
                'job' => 'Illuminate\Queue\CallQueuedHandler@call',
                'maxTries' => null,
                'timeout' => null,
                'timeoutAt' => null,
                'data' => [
                    'commandName' => FakeJob::class,
                    'command' => serialize($job),
                ],
                'attempts' => 0,
            ]),
            'attributes' => [
                'ApproximateReceiveCount' => 1,
            ],
            'messageAttributes' => [],
            'eventSourceARN' => 'arn:aws:sqs:us-east-1:959512994844:servotizer-test-queue-2',
            'awsRegion' => 'us-east-1',
        ]));

        $this->artisan('servotizer:work', ['message' => $message]);

        $this->assertTrue(FakeJob::$handled);
    }

    /**
     * Get the package's service providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \Apsonex\ServotizerCore\ServotizerServiceProvider::class,
        ];
    }

    /**
     * Define the environment.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('queue.connections.servotizer', [
            'driver' => 'sqs',
            'key' => env('SQS_KEY', 'your-public-key'),
            'secret' => env('SQS_SECRET', 'your-secret-key'),
            'prefix' => env('SQS_PREFIX', 'https://sqs.us-east-1.amazonaws.com/your-account-id'),
            'queue' => env('SQS_QUEUE', 'your-queue-name'),
            'region' => env('SQS_REGION', 'us-east-1'),
            'delay' => env('SQS_DELAY', 0),
            'tries' => env('SQS_TRIES', 0),
            'force' => env('SQS_FORCE', false),
        ]);
    }
}
