<?php namespace Barryvdh\Queue\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Barryvdh\Queue\Jobs\AsyncJob;

class AsyncCommand extends Command {

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
	protected $description = 'Run a base64+json encode serialized queue';

	/**
	 * Create a new command instance.
	 *
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
        $payload = $this->argument('payload');

        $job = new AsyncJob($this->laravel, $payload);

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
			array('payload', InputArgument::REQUIRED, 'The Job Payload'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

}