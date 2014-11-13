<?php

namespace Barryvdh\Queue\Connectors;

use Barryvdh\Queue\AsyncQueue;
use Illuminate\Queue\Connectors\ConnectorInterface;

class AsyncConnector implements ConnectorInterface
{
    /**
     * Establish a queue connection.
     *
     * @param array $config
     *
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function connect(array $config)
    {
        return new AsyncQueue();
    }
}
