<?php

namespace Barryvdh\Queue\Console;

use Barryvdh\Queue\Jobs\AsyncJob;
use Barryvdh\Queue\Models\Job;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class AsyncCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'queue:async';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a queue from the database';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $item = Job::findOrFail($this->argument('job_id'));

        $job = new AsyncJob($this->laravel, $item);

        $job->fire();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['job_id', InputArgument::REQUIRED, 'The Job ID'],
        ];
    }
}
