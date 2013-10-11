<?php namespace Packettide\Bree;

class FieldSetProvider {

	protected static $fieldsets = array();


	/**
	 * Register a new FieldSet
	 * @param  Packettide\Bree\FieldSet|string $fieldset
	 * @return [type]           [description]
	 */
	public static function register($fieldset)
	{
		if(!is_object($fieldset) && class_exists($fieldset))
		{
			$fieldset = new $fieldset;
			$fieldset::sortAssets();
		}
		
		self::addFieldSet($fieldset);
		
	}

	/**
	 * Attach an array of FieldTypes to an already defined FieldSet
	 * @param  string $fieldset 
	 * @param  array  $fields   
	 * @return [type]           [description]
	 */
	public static function attachFields($fieldset, $fields = array())
	{
		if(array_key_exists($fieldset, self::$fieldsets))
		{
			self::$fieldsets[$fieldset]->attach($fields);
		}
		else
		{
			// Error - FieldSet not found
			echo 'fieldset not found';
		}
	}

	/**
	 * [loaded description]
	 * @return [type] [description]
	 */
	public static function loaded()
	{
		return self::$fieldsets;
	}


	public static function getFieldType($name)
	{
		$fieldtypes = array();

		// Loop through all registered fieldsets and search for the named fieldtype
		// if found add to array with the fieldset as key
		// the model will deal with multiple fieldtype implementations across sets
		foreach(self::$fieldsets as $fieldset)
		{
			$fieldtype = $fieldset->retrieveFieldType($name);
			
			if($fieldtype)
			{
				$fieldtypes[$fieldset::getName()] = $fieldtype;
			}
		}

		return $fieldtypes;
	}


	private static function addFieldSet($fieldset)
	{
		if( !($fieldset instanceof FieldSet) ) echo 'error - fieldset not recognized';

		if(!array_key_exists($fieldset::getName(), self::$fieldsets))
		{
			self::$fieldsets[$fieldset::getName()] = $fieldset;
		}
		else
		{
			echo 'error - fieldset already registered';
		}
	}	

}