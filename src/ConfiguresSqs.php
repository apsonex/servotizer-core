<?php

namespace Apsonex\ServotizerCore;

use Illuminate\Support\Facades\Config;

trait ConfiguresSqs
{
    /**
     * Ensure SQS is properly configured.
     *
     * @return void
     */
    protected function ensureSqsIsConfigured()
    {
        // Ensure we are running on servotizer...
        if (! isset($_ENV['SERVOTIZER_SSM_PATH'])) {
            return;
        }

        Config::set('queue.connections.sqs', array_merge([
            'driver' => 'sqs',
            'key' => $_ENV['AWS_ACCESS_KEY_ID'] ?? null,
            'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'] ?? null,
            'prefix' => $_ENV['SQS_PREFIX'] ?? null,
            'queue' => $_ENV['SQS_QUEUE'] ?? 'default',
            'region' => $_ENV['AWS_DEFAULT_REGION'] ?? 'us-east-1',
        ], Config::get('queue.connections.sqs') ?? []));
    }
}
