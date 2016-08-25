<?php

namespace Armxy\Queue\Connectors;

use Armxy\Queue\InternalQueue;
use Illuminate\Queue\Connectors\ConnectorInterface;

class InternalConnector implements ConnectorInterface
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
        return new InternalQueue($config);
    }
}
