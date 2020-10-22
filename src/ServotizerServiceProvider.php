<?php

namespace Apsonex\ServotizerCore;

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;
use Apsonex\ServotizerCore\Console\Commands\ServotizerWorkCommand;
use Apsonex\ServotizerCore\Http\Controllers\SignedStorageUrlController;
use Apsonex\ServotizerCore\Http\Middleware\ServeStaticAssets;
use Apsonex\ServotizerCore\Queue\ServotizerConnector;

class ServotizerServiceProvider extends ServiceProvider
{
    use ConfiguresAssets, ConfiguresDynamoDb, ConfiguresQueue, ConfiguresRedis, ConfiguresSqs, DefinesRoutes;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->ensureRoutesAreDefined();

        //        if (($_ENV['VAPOR_SERVERLESS_DB'] ?? null) === 'true') {
        //            Schema::defaultStringLength(191);
        //        }

        if ($this->app->resolved('queue')) {
            call_user_func($this->queueExtender());
        } else {
            $this->app->afterResolving(
                'queue', $this->queueExtender()
            );
        }
    }

    /**
     * Get the queue extension callback.
     *
     * @return \Closure
     */
    protected function queueExtender()
    {
        return function () {
            Queue::extend('sqs', function () {
                return new ServotizerConnector;
            });
        };
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            Contracts\SignedStorageUrlController::class,
            SignedStorageUrlController::class
        );

        $this->configure();
        $this->offerPublishing();
        $this->ensureAssetPathsAreConfigured();
        $this->ensureRedisIsConfigured();
        $this->ensureDynamoDbIsConfigured();
        $this->ensureQueueIsConfigured();
        $this->ensureSqsIsConfigured();
        $this->ensureMixIsConfigured();
        $this->configureTrustedProxy();

        $this->registerMiddleware();
        $this->registerCommands();
    }

    /**
     * Setup the configuration for Horizon.
     *
     * @return void
     */
    protected function configure()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/servotizer.php', 'servotizer'
        );
    }

    /**
     * Setup the resource publishing groups for Horizon.
     *
     * @return void
     */
    protected function offerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/servotizer.php' => config_path('servotizer.php'),
            ], 'servotizer-config');
        }
    }

    /**
     * Ensure Laravel Mix is properly configured.
     *
     * @return void
     */
    protected function ensureMixIsConfigured()
    {
        if (isset($_ENV['MIX_URL'])) {
            Config::set('app.mix_url', $_ENV['MIX_URL']);
        }
    }

    /**
     * Configure trusted proxy.
     *
     * @return void
     */
    private function configureTrustedProxy()
    {
        Config::set('trustedproxy.proxies', Config::get('trustedproxy.proxies') ?? ['0.0.0.0/0', '2000:0:0:0:0:0:0:0/3']);
    }

    /**
     * Register the servotizer HTTP middleware.
     *
     * @return void
     */
    protected function registerMiddleware()
    {
        $this->app[HttpKernel::class]->pushMiddleware(ServeStaticAssets::class);
    }

    /**
     * Register the servotizer console commands.
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function registerCommands()
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->app[ConsoleKernel::class]->command('servotizer:handle {payload}', function () {
            throw new InvalidArgumentException(
                'Unknown event type. Please create a servotizer:handle command to handle custom events.'
            );
        });

        $this->app->singleton('command.servotizer.work', function ($app) {
            return new ServotizerWorkCommand($app['queue.servotizerWorker']);
        });

        $this->commands(['command.servotizer.work']);
    }
}
