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
        $this->registerAsyncConnector($this->app['queue']);

        $this->commands('command.queue.async');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerAsyncCommand();
    }

    /**
     * Register the queue listener console command.
     *
     *
     * @return void
     */
    protected function registerAsyncCommand()
    {
        $this->app->singleton('command.queue.async', function () {
             return new AsyncCommand($this->app['command.queue.work']);
        });
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
            return new AsyncConnector($this->app['db']);
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
