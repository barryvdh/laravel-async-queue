<?php

namespace Barryvdh\Queue;

use Barryvdh\Queue\Connectors\AsyncConnector;
use Barryvdh\Queue\Console\AsyncCommand;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class AsyncServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Add the connector to the queue drivers.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerAsyncConnector($this->app['queue']);
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
            return new AsyncConnector;
        });
    }
}
