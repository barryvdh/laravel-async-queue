<?php namespace Barryvdh\Queue\Jobs;

use Illuminate\Queue\Jobs\SyncJob;
use Illuminate\Container\Container;

class AsyncJob extends SyncJob {

    /**
     * The payload, Base64 & JSON Encoded
     *
     * @var array
     */
    protected $payload;

    /**
     * Create a new job instance.
     *
     * @param  \Illuminate\Container\Container  $container
     * @param  string   $payload
     */
    public function __construct(Container $container, $payload)
    {
        $this->payload = $payload;
        $this->container = $container;
    }

    /**
     * Fire the job.
     *
     * @return void
     */
    public function fire()
    {
        $payload = $this->parsePayload($this->payload);

        $this->resolveAndFire($payload);
    }


    /**
     * Parse the payload to an array.
     *
     * @param $payload
     * @return array|null
     */
    protected function parsePayload($payload){
        return json_decode(base64_decode($payload), true);
    }


}