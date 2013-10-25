<?php namespace Packettide\Bree;

use Illuminate\Support\ServiceProvider;

class BreeServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('packettide/bree');

		$this->app['command.bree.assets'] = $this->app->share(function($app)
		{
			return new Commands\AssetsCommand;
		});

		$this->commands('command.bree.assets');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{

		// Register the Basic FieldSet
		FieldSetProvider::register('Packettide\Bree\FieldSets\BasicFieldSet');

		// Register the Advanced FieldSet
		FieldSetProvider::register('Packettide\BreeFsAdvanced\AdvancedFieldSet');

		// Attach first party fields
		FieldSetProvider::attachFields('basic', array(
			'Date' => 'Packettide\Bree\FieldTypes\Date',
			'File' => 'Packettide\Bree\FieldTypes\File',
			'InlineStacked' => 'Packettide\Bree\FieldTypes\Relate', // keep this alias around for a bit
			'Relate' => 'Packettide\Bree\FieldTypes\Relate',
			'Matrix' => 'Packettide\Bree\FieldTypes\None',
			'Text' => 'Packettide\Bree\FieldTypes\Text',
			'TextArea' => 'Packettide\Bree\FieldTypes\TextArea',
			'Time' => 'Packettide\Bree\FieldTypes\Time'
		));

		$this->app['bree'] = $this->app->share(function($app)
		{
			return new Model;
		});

		$this->app->booting(function()
		{
			$loader = \Illuminate\Foundation\AliasLoader::getInstance();
			$loader->alias('Bree', 'Packettide\Bree\Model');
		});


	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('bree');
	}

}