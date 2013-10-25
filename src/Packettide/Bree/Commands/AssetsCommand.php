<?php namespace Packettide\Bree\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class AssetsCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'bree:assets';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Publishes assets for registered field packages.';

	/**
	 * Create a new command instance.
	 *
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
		// @todo loop through all registered field packages and call asset:publish on their root namespace
		$this->call('asset:publish', array('package' => 'packettide/bree'));
		$this->call('asset:publish', array('package' => 'packettide/bree-fs-advanced'));
	}
}