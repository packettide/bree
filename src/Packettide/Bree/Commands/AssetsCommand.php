<?php namespace Packettide\Bree\Commands;

use Packettide\Bree\FieldSetProvider;
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
		//$this->call('asset:publish', array('package' => 'packettide/bree'));
		//$this->call('asset:publish', array('package' => 'packettide/bree-fs-advanced'));

		$packages = array();
		$fieldsets = FieldSetProvider::all();

		# This is all bad but I wanted a solution to this sooner than rewriting asset handling
		# @todo clean this up when new asset handling is implemented
		foreach($fieldsets as $fieldset)
		{
			if(!empty($fieldset->getAssets()))
			{
				$packages = $this->addPackageClass(get_class($fieldset), $packages);
			}

			$fieldtypes = $fieldset->allFieldTypes();
			foreach($fieldtypes as $fieldtype)
			{
				if(!empty($fieldtype::assets()))
				{
					$packages = $this->addPackageClass($fieldtype, $packages);
				}
			}
		}

		foreach($packages as $package)
		{
			try
			{
				$this->call('asset:publish', array('package' => $package));
			}
			catch(\Exception $e)
			{
				echo 'Error for package: '. $package .' - '. $e->getMessage() . "\n";
			}
		}
	}

	public function addPackageClass($class, $packages)
	{
		$packageName = $this->findPackageName($class);
		$packagePath = $this->getPackagePath($packageName);

		if(!in_array($packagePath, $packages))
		{
			$packages[] = $packagePath;
		}

		return $packages;
	}

	public function findPackageName($class)
	{
		$packageName = preg_match("/[A-Za-z0-9-]*\\\\[A-Za-z0-9-]*/", $class, $matches);

		return ($matches) ? $matches[0] : '';
	}

	public function getPackagePath($packageName)
	{
		// convert StudlyCase to dash-separated-names
		$pattern = '/([a-z])([A-Z])/';
		$replacement = '$1-$2';
		$packagePath = preg_replace($pattern, $replacement, $packageName);
		$packagePath = str_replace('\\', '/', $packagePath);

		return strtolower($packagePath);
	}

}