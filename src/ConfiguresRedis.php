<?php

namespace Apsonex\ServotizerCore;

use Illuminate\Support\Facades\Config;

trait ConfiguresRedis
{
    /**
     * Ensure Redis is properly configured.
     *
     * @return void
     */
    protected function ensureRedisIsConfigured()
    {
        if (! isset($_ENV['SERVOTIZER_CACHE']) || $_ENV['SERVOTIZER_CACHE'] !== 'true') {
            return;
        }

        Config::set('database.redis', [
            'client' => $_ENV['REDIS_CLIENT'] ?? 'phpredis',
            'options' => array_merge(Config::get('database.redis.options', []), [
                'cluster' => $_ENV['REDIS_CLUSTER'] ?? 'redis',
            ]),
            'clusters' => array_merge(Config::get('database.redis.clusters', []), [
                'default' => [
                    [
                        'host' => $_ENV['REDIS_HOST'] ?? '127.0.0.1',
                        'password' => null,
                        'port' => 6379,
                        'database' => 0,
                    ],
                ],
                'cache' => [
                    [
                        'host' => $_ENV['REDIS_HOST'] ?? '127.0.0.1',
                        'password' => null,
                        'port' => 6379,
                        'database' => 0,
                    ],
                ],
            ]),
        ]);
    }
}
