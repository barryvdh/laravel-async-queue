<?php

namespace Barryvdh\Queue\Connectors;

use Barryvdh\Queue\AsyncQueue;
use Illuminate\Queue\Connectors\DatabaseConnector;
use Illuminate\Support\Arr;

class AsyncConnector extends DatabaseConnector
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
        return new AsyncQueue(
			$this->connections->connection(Arr::get($config, 'connection')),
			$config['table'],
			$config['queue'],
			Arr::get($config, 'expire', 60),
            Arr::get($config, 'binary', 'php'),
            Arr::get($config, 'binary_args', ''),
            Arr::get($config, 'connection_name', '')
		);
    }
}
