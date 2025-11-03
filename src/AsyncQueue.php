<?php
namespace Barryvdh\Queue;

use Illuminate\Queue\SyncQueue;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\Queue;

class AsyncQueue extends SyncQueue
{
    /**
     * {@inheritdoc}
     */
    public function push($job, $data = '', $queue = null)
    {
        Concurrency::driver('process')
            ->defer(fn () => Queue::connection('sync')->push($job, $data, $queue));
    }
}
