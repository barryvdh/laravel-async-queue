<?php

namespace Armxy\Queue;

use \Armxy\Queue\Connectors\InternalConnector;
use \Armxy\Queue\Console\InternalCommand;
use \Illuminate\Support\ServiceProvider;

class InternalQueueServiceProvider extends ServiceProvider
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
        $this->registerInternalConnector($this->app['queue']);

        $this->commands('command.queue.internal');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerInternalCommand($this->app);
    }

    /**
     * Register the queue listener console command.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function registerInternalCommand($app)
    {
        $app['command.queue.internal'] = $app->share(function ($app) {
            return new InternalCommand();
        });
    }

    /**
     * Register the Internal queue connector.
     *
     * @param \Illuminate\Queue\QueueManager $manager
     *
     * @return void
     */
    protected function registerInternalConnector($manager)
    {
        $manager->addConnector('internal', function () {
            return new InternalConnector();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('command.queue.internal');
    }
}
