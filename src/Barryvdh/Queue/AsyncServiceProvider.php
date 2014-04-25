<?php namespace Barryvdh\Queue;

use Illuminate\Support\ServiceProvider;
use Barryvdh\Queue\Connectors\AsyncConnector;
use Barryvdh\Queue\Console\AsyncCommand;

class AsyncServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

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
     * Add the connector to the queue drivers
     */
    public function boot(){
        $manager = $this->app['queue'];
        $this->registerAsyncConnector($manager);
	}

    /**
     * Register the queue listener console command.
     *
     * @return void
     */
    protected function registerAsyncCommand()
    {
        $app = $this->app;

        $app['command.queue.async'] = $app->share(function($app)
            {
                return new AsyncCommand();
            });

        $this->commands('command.queue.async');
    }

    /**
     * Register the Async queue connector.
     *
     * @param  \Illuminate\Queue\QueueManager  $manager
     * @return void
     */
    protected function registerAsyncConnector($manager)
    {
        $manager->addConnector('async', function()
            {
                return new AsyncConnector;
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