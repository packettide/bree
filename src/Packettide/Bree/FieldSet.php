<?php namespace Packettide\Bree;

abstract class FieldSet {

	protected $fieldtypes = array();
	protected static $assets = array();
	public $name;

	/**
	 * Retrieve the FieldSet's name
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Get a list of all associated FieldTypes
	 * @return [type] [description]
	 */
	public function allFieldTypes()
	{
		return $this->fieldtypes;
	}

	/**
	 * Retrieve the requested FieldType if present
	 * @param  string $name
	 * @return string|Packettide/Bree/FieldType
	 */
	public function retrieveFieldType($name)
	{
		$fieldtype = '';

		if(array_key_exists($name, $this->fieldtypes))
		{
			$fieldtype = $this->fieldtypes[$name];
		}

		return $fieldtype;
	}

	/**
	 * Add one or more FieldTypes to the FieldSet
	 * @param  array  $fieldtypes [description]
	 * @return
	 */
	public function attach($fieldtypes = array())
	{
		$this->fieldtypes = array_merge($this->fieldtypes, $fieldtypes);
	}

	/**
	 * Return FieldSet's assets
	 * @return array
	 */
	public function getAssets()
	{
		return static::$assets;
	}

	/**
	 * Retrieve the assets for a fieldset
	 * @return array
	 */
	public static function assets()
	{
		if(is_array(static::$assets))
		{
			static::$assets = new Assets\Bundle(static::$assets);
		}

		return static::$assets;
	}

	/**
	 * Generate an array of assets for all dependent FieldTypes
	 * @return array
	 */
	public function fieldTypeAssets()
	{
		$assets = new Assets\Collection;

		foreach($this->fieldtypes as $fieldtype)
		{
			$assets->put($fieldtype, $fieldtype::assets());
		}
		// echo '--';
		// var_dump($assets);
		// echo '--';
		return $assets;
	}

}