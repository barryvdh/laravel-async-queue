<?php namespace Barryvdh\Queue;

use Illuminate\Queue\Queue;
use Illuminate\Queue\QueueInterface;
use Symfony\Component\Process\Process;

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
        $payload = $this->createPayload($job, $data);

        $process = $this->makeProcess($payload);

        $process->run();


        return 0;
    }

    /**
     * Create a payload string from the given job and data.
     *
     * @param  string $job
     * @param  mixed  $data
     * @param  string $queue
     * @return string
     */
    protected function createPayload($job, $data = '', $queue = null)
    {
        $payload = parent::createPayload($job, $data, $queue);

        return base64_encode($payload);
    }

    /**
     * Make a Process for the Artisan command with the payload
     *
     * @param $payload
     * @return \Symfony\Component\Process\Process
     */
    public function makeProcess($payload)
    {
        $environment = $this->container->environment();
        $cwd = $this->container['path.base'];

        $string = 'php artisan queue:async %s --env=%s';

        if (substr(php_uname(), 0, 7) == "Windows"){  
            $string = 'start /B ' . $string . ' > NUL'; 
        } 
        else { 
            $string .= ' > /dev/null 2>&1 &';
        } 

        $command = sprintf($string, $payload, $environment);

        return new Process($command, $cwd);
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
        return $this->push($job, $data, $queue);
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
