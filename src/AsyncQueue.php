<?php namespace Barryvdh\Queue;

use Illuminate\Queue\Queue;
use Illuminate\Queue\QueueInterface;
use Symfony\Component\Process\Process;
use Barryvdh\Queue\Models\Job;

class AsyncQueue extends Queue implements QueueInterface {

    /**
     * Push a new job onto the queue.
     *
     * @param  string  $job
     * @param  mixed   $data
     * @param  string  $queue
     * @return mixed
     */
    public function push($job, $data = '', $queue = null)
    {
        $id = $this->storeJob($job, $data);
        $this->startProcess($id, 0);
        return 0;
    }

    /**
     * Store the job in the database
     * 
     * @param  string  $job
     * @param  mixed   $data
     * @param  integer $delay
     * @return integer The id of the job
     */
    public function storeJob($job, $data, $delay = 0){

        $payload = $this->createPayload($job, $data);

        $job = new Job;
        $job->status = Job::STATUS_OPEN;
        $job->delay = $delay;
        $job->payload = $payload;
        $job->save();

        return $job->id;
    }

    /**
     * Make a Process for the Artisan command for the job id
     *
     * @param  integer $jobId
     */
    public function startProcess($jobId)
    {
        $environment = $this->container->environment();
        $cwd = $this->container['path.base'];
        $string = 'php artisan queue:async %d --env=%s ';
        if (defined('PHP_WINDOWS_VERSION_BUILD')){
            $string = 'start /B ' . $string . ' > NUL';
        } else {
            $string = 'nohup ' . $string . ' > /dev/null 2>&1 &';
        }

        $command = sprintf($string, $jobId, $environment);
        $process = new Process($command, $cwd);
        $process->run();
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param  \DateTime|int  $delay
     * @param  string  $job
     * @param  mixed  $data
     * @param  string  $queue
     * @return mixed
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
     * @param  string  $queue
     * @return \Illuminate\Queue\Jobs\Job|null
     */
    public function pop($queue = null) {}


    /**
     * Push a raw payload onto the queue.
     *
     * @param  string $payload
     * @param  string $queue
     * @param  array $options
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = array())
    {
        //
    }
}
