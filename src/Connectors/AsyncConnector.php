<?php

namespace Barryvdh\Queue\Connectors;

use Barryvdh\Queue\AsyncQueue;
use Illuminate\Queue\Connectors\SyncConnector;

class AsyncConnector extends SyncConnector
{
    /**
     * {@inheritdoc}
     */
    public function connect(array $config)
    {
        return new AsyncQueue($config['after_commit'] ?? null);
    }
}
