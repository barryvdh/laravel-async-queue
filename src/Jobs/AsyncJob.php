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
     *
     * @return void
     */
    public function __construct(Container $container, Job $job)
    {
        $this->job = $job;
        $this->container = $container;
    }

    /**
     * Fire the job.
     *
     * @return void
     */
    public function fire()
    {
        // Get the payload from the job
        $payload = $this->parsePayload($this->job->payload);

        // If we have to wait, sleep until our time has come
        if ($this->job->delay) {
            $this->job->status = Job::STATUS_WAITING;
            $this->job->save();
            sleep($this->job->delay);
        }

        // Mark job as started
        $this->job->status = Job::STATUS_STARTED;
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
}
