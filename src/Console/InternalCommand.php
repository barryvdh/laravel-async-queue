<?php

namespace Armxy\Queue\Console;

use \Armxy\Queue\Jobs\InternalJob;
use \Armxy\Queue\Models\Job;
use \Illuminate\Console\Command;
use \Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\InputOption;

class InternalCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'queue:internal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a queue from the database';

    /**
     * Create a new queue listen command.
     *
     * @param  \Illuminate\Queue\Worker  $worker
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        \DB::connection()->reconnect();
        $item = Job::lock()->findOrFail($this->argument('job_id'));

        if ($delay = (int)$this->option('delay')) {
            sleep($delay);
        }

        $job = new InternalJob($this->laravel, $item);

        $job->fire();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('job_id', InputArgument::REQUIRED, 'The Job ID'),
        );
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('delay', 'D', InputOption::VALUE_OPTIONAL, 'The delay in seconds', 0),
        );
    }
}
