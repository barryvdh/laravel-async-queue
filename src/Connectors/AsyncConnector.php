<?php

namespace Barryvdh\Queue\Connectors;

use Barryvdh\Queue\AsyncQueue;
use Illuminate\Queue\Connectors\ConnectorInterface;

class AsyncConnector implements ConnectorInterface
{
    /**
     * Default configuration
     *
     * @var array
     */
    protected $defaults = array(
        'binary'        => 'php',
        'binary_args'   => '',
    );

    /**
     * Establish a queue connection.
     *
     * @param array $config
     *
     * @return \Illuminate\Queue\QueueInterface
     */
    public function connect(array $config)
    {
        $config = array_merge($this->defaults, $config);
        return new AsyncQueue($config);
    }
}
