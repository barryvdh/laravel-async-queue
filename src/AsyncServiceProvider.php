<?php

namespace Barryvdh\Queue;

use Barryvdh\Queue\Connectors\AsyncConnector;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\ServiceProvider;

class AsyncServiceProvider extends ServiceProvider
{
    /**
     * Add the connector to the queue drivers.
     *
     * @return void
     */
    public function register()
    {
        $this->registerAsyncConnector();
    }

    /**
     * Register the Async queue connector.
     *
     * @return void
     */
    protected function registerAsyncConnector()
    {
        $this->callAfterResolving(QueueManager::class, function ($manager) {
            $manager->addConnector('async', function () {
                return new AsyncConnector;
            });
        });
    }
}
