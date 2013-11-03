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

		$packages = array();
		$fieldsets = FieldSetProvider::all();

		foreach($fieldsets as $fieldset)
		{
			if(!$fieldset::assets()->isEmpty())
			{
				$packages = $this->addPackageClass(get_class($fieldset), $packages);
			}

			$fieldtypes = $fieldset->allFieldTypes();

			foreach($fieldtypes as $fieldtype)
			{
				if(!$fieldtype::assets()->isEmpty())
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
				// couldn't publish package assets from vendor
				// but lets try the workbench too
				try
				{
					$this->call('asset:publish', array('--bench' => $package));
					$this->comment('Assets published from workbench: '.$package);
				}
				catch(\Exception $e)
				{
					$this->error($package .' - '. $e->getMessage());
				}
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