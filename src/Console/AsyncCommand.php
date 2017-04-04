<?php

namespace Barryvdh\Queue\Console;

use Barryvdh\Queue\AsyncQueue;
use Illuminate\Console\Command;
use Illuminate\Queue\DatabaseQueue;
use Illuminate\Queue\Worker;
use Illuminate\Queue\WorkerOptions;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

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
    protected $description = 'Run a queue job from the database';
    
    /**
	 * The queue worker instance.
	 *
	 * @var \Illuminate\Queue\Worker
	 */
	protected $worker;

	/**
	 * Create a new queue listen command.
	 *
	 * @param  \Illuminate\Queue\Worker  $worker
	 */
	public function __construct(Worker $worker)
	{
		parent::__construct();

		$this->worker = $worker;
	}

    /**
     * Execute the console command.
     *
     * @param WorkerOptions $options
     * @return void
     */
    public function fire(WorkerOptions $options)
    {
        $id = $this->argument('id');
        $connection = $this->argument('connection');
        
        $this->processJob(
			$connection, $id, $options
		);
    }

    /**
     *  Process the job
     * @param string $connectionName
     * @param integer $id
     * @param WorkerOptions $options
     */
    protected function processJob($connectionName, $id, $options)
    {
        $manager = $this->worker->getManager();

        /** @var AsyncQueue $connection */
        $connection = $manager->connection($connectionName);
        
		$job = $connection->getJobFromId($id);

		if ( ! is_null($job)) {
			$this->worker->process(
				$manager->getName($connectionName), $job, $options
			);
		}
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('id', InputArgument::REQUIRED, 'The Job ID'),
            array('connection', InputArgument::OPTIONAL, 'The name of connection'),
        );
    }
}
