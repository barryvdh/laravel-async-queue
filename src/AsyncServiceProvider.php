<?php

namespace Barryvdh\Queue;

use Barryvdh\Queue\Connectors\AsyncConnector;
use Barryvdh\Queue\Console\AsyncCommand;
use Illuminate\Support\ServiceProvider;

class AsyncServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Add the connector to the queue drivers.
     *
     * @return void
     */
    public function boot()
    {
        $manager = $this->app['queue'];
        $this->registerAsyncConnector($manager);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerAsyncCommand($this->app);
    }

    /**
     * Register the queue listener console command.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function registerAsyncCommand($app)
    {
        $app['command.queue.async'] = $app->share(function ($app) {
                return new AsyncCommand();
            });

        $this->commands('command.queue.async');
    }

    /**
     * Register the Async queue connector.
     *
     * @param \Illuminate\Queue\QueueManager $manager
     *
     * @return void
     */
    protected function registerAsyncConnector($manager)
    {
        $manager->addConnector('async', function () {
                return new AsyncConnector();
            });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('command.queue.async');
    }
}
