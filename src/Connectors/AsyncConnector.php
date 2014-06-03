<?php namespace Barryvdh\Queue\Connectors;

use Illuminate\Queue\Connectors\ConnectorInterface;
use Barryvdh\Queue\AsyncQueue;

class AsyncConnector implements ConnectorInterface {

    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     * @return \Illuminate\Queue\QueueInterface
     */
    public function connect(array $config)
    {
        return new AsyncQueue;
    }

}