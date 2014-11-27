<?php

namespace Barryvdh\Queue\Jobs;

use Barryvdh\Queue\Models\Job;
use Illuminate\Container\Container;
use Illuminate\Queue\Jobs\SyncJob;

class AsyncJob extends SyncJob
{
    /**
     * The job model.
     *
     * @var Job
     */
    protected $job;

    /**
     * Create a new job instance.
     *
     * @param \Illuminate\Container\Container $container
     * @param \Barryvdh\Queue\Models\Job      $job
     */
    public function __construct(Container $container, Job $job)
    {
        $this->container = $container;
        $this->job = $job;
    }

    /**
     * Fire the job.
     *
     * @return void
     */
    public function fire()
    {
        // Get the payload from the job
        $payload = $this->parsePayload($this->getRawBody());

        // Mark job as started
        $this->job->status = Job::STATUS_STARTED;
        $this->job->retries++;
        $this->job->save();

        // Fire the actual job
        $this->resolveAndFire($payload);

        // If job is not deleted, mark as finished
        if (!$this->deleted) {
            $this->job->status = Job::STATUS_FINISHED;
            $this->job->save();
        }
    }

    /**
     * Get the raw body string for the job.
     *
     * @return string
     */
    public function getRawBody()
    {
        return $this->job->payload;
    }

    /**
     * Release the job back into the queue.
     *
     * @param  int   $delay
     * @return void
     */
    public function release($delay = 0)
    {
        // Update the Job status
        $this->job->status = Job::STATUS_OPEN;
        $this->job->save();

        // Wait for the delay
        if ($delay) {
            sleep($this->getSeconds($delay));
        }

        // Fire again
        $this->fire();
    }

    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     */
    public function attempts()
    {
        return (int) $this->job->retries;
    }

    /**
     * Delete the job from the queue.
     *
     * @return void
     */
    public function delete()
    {
        parent::delete();
        $this->job->delete();
    }

    /**
     * Parse the payload to an array.
     *
     * @param string $payload
     *
     * @return array|null
     */
    protected function parsePayload($payload)
    {
        return json_decode($payload, true);
    }

    /**
     * Get the job identifier.
     *
     * @return string
     */
    public function getJobId()
    {
        return $this->job->id;
    }
}
