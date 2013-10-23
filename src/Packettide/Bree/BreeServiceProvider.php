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
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{

		// Register the Basic + Advanced FieldSets
		FieldSetProvider::register('Packettide\Bree\FieldSets\BasicFieldSet');
		FieldSetProvider::register('Packettide\Bree\FieldSets\AdvancedFieldSet');

		// Attach first party fields
		FieldSetProvider::attachFields('basic', array(
			'Date' => 'Packettide\Bree\FieldTypes\Date',
			'File' => 'Packettide\Bree\FieldTypes\File',
			'InlineStacked' => 'Packettide\Bree\FieldTypes\InlineStacked',
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