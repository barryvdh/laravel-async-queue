<?php
namespace Barryvdh\Queue;

use Barryvdh\Queue\Models\Job;
use Illuminate\Queue\SyncQueue;

class AsyncQueue extends SyncQueue
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
     *
     * @return int
     */
    public function push($job, $data = '', $queue = null)
    {
        $id = $this->storeJob($job, $data, 0);
        $this->startProcess($id, 0);

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
        chdir($this->container['path.base']);
        exec($this->getCommand($jobId, $delay));
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
        $cmd = '%s artisan queue:async %d --env=%s --delay=%d';
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
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            return 'start /B '.$cmd.' > NUL';
        } else {
            return $cmd.' > /dev/null 2>&1 &';
        }
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

}
