<?php
namespace Armxy\Queue;

use \Armxy\Queue\Models\Job;
use \Armxy\Queue\Models\Queue;
use \Armxy\Queue\Jobs\InternalJob;
use \Illuminate\Queue\SyncQueue;
use \Symfony\Component\Process\Process;

class InternalQueue extends SyncQueue
{
    /** @var array */
    protected $config;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Push a new job onto the queue.
     *
     * @param string      $job
     * @param mixed       $data
     * @param string|null $queue
     * @param bool        $startNow
     *
     * @return int
     */
    public function push($job, $data = '', $queue = null, $startNow = false)
    {
        $id = $this->storeJob($job, $data, 0);

        if($startNow){

            $this->startProcess($id, 0);
        }

        return $id;
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
     * @param int $delay
     *
     * @return void
     */
    public function startProcess($jobId, $delay = 0)
    {
        $command = $this->getCommand($jobId, $delay);
        $cwd = $this->container['path.base'];
        $process = new Process($command, $cwd);
        $process->run();

        //chdir($this->container['path.base']);
        //exec($this->getCommand($jobId, $delay));
    }

    /**
     * Get the Artisan command as a string for the job id.
     *
     * @param int $jobId
     * @param int $delay
     *
     * @return string
     */
    protected function getCommand($jobId, $delay = 0)
    {
        $cmd = '%s artisan queue:internal %d --env=%s --delay=%d';
        $cmd = $this->getBackgroundCommand($cmd);

        $binary = $this->getPhpBinary();
        $environment = $this->container->environment();

        return sprintf($cmd, $binary, $jobId, $environment, $delay);
    }

    /**
     * Get the escaped PHP Binary from the configuration
     *
     * @return string
     */
    protected function getPhpBinary()
    {
        $path = $this->config['binary'];
        if (!defined('PHP_WINDOWS_VERSION_BUILD')) {
            $path = escapeshellarg($path);
        }
        
        $args = $this->config['binary_args'];
        if(is_array($args)){
            $args = implode(' ', $args);
        }
        return trim($path.' '.$args);
    }

    protected function getBackgroundCommand($cmd)
    {
        return $cmd.' > /dev/null 2>&1 &';
        /*
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            return 'start /B '.$cmd.' > NUL';
        } else {
            return $cmd.' > /dev/null 2>&1 &';
        }
        */
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
        $this->startProcess($id, $delay);

        return $id;
    }


    /**
     * Get next queued job from database.
     *
     * @param string|null        $queue
     *
     * @return int
     */
    public function pop($queue = null){

        $queueModel = new Queue();

        $firstJob = $queueModel->getFirstQueue();

        if($firstJob !== null){

            $job = new InternalJob($this->container,  $firstJob);

            return $job;
        }

        return null;
    }

}
