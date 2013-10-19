<?php namespace Packettide\Bree\FieldTypes;

use Packettide\Bree\FieldType;

/**
 * Dummy field for testing
 */
class None extends FieldType {

	public function field($attributes = array())
	{
		return '';
	}

}