<?php

namespace Barryvdh\Queue;

use Barryvdh\Queue\Models\Job;
use Illuminate\Queue\Queue;
use Illuminate\Queue\QueueInterface;
use Symfony\Component\Process\Process;

class AsyncQueue extends Queue implements QueueInterface
{
    /**
     * Push a new job onto the queue.
     *
     * @param string      $job
     * @param mixed       $data
     * @param string|null $queue
     *
     * @return int
     */
    public function push($job, $data = '', $queue = null)
    {
        $id = $this->storeJob($job, $data);
        $this->startProcess($id, 0);

        return 0;
    }

    /**
     * Store the job in the database.
     *
     * Returns the id of the job.
     *
     * @param string $job
     * @param mixed  $data
     * @param int    $delay
     *
     * @return int
     */
    public function storeJob($job, $data, $delay = 0)
    {
        $payload = $this->createPayload($job, $data);

        $job = new Job();
        $job->status = Job::STATUS_OPEN;
        $job->delay = $delay;
        $job->payload = $payload;
        $job->save();

        return $job->id;
    }

    /**
     * Make a Process for the Artisan command for the job id.
     *
     * @param int $jobId
     *
     * @return void
     */
    public function startProcess($jobId)
    {
        $environment = $this->container->environment();
        $cwd = $this->container['path.base'];
        $string = 'php artisan queue:async %d --env=%s ';
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $string = 'start /B '.$string.' > NUL';
        } else {
            $string = 'nohup '.$string.' > /dev/null 2>&1 &';
        }

        $command = sprintf($string, $jobId, $environment);
        $process = new Process($command, $cwd);
        $process->run();
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param \DateTime|int $delay
     * @param string        $job
     * @param mixed         $data
     * @param string|null   $queue
     *
     * @return int
     */
    public function later($delay, $job, $data = '', $queue = null)
    {
        $delay = $this->getSeconds($delay);
        $id = $this->storeJob($job, $data, $delay);
        $this->startProcess($id);

        return 0;
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param string|null $queue
     *
     * @return void
     */
    public function pop($queue = null)
    {
        // do nothing
    }

    /**
     * Push a raw payload onto the queue.
     *
     * @param string      $payload
     * @param string|null $queue
     * @param array       $options
     *
     * @return void
     */
    public function pushRaw($payload, $queue = null, array $options = array())
    {
        // do nothing
    }
}
