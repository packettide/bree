<?php namespace Packettide\Bree\FieldTypes;

use Packettide\Bree\FieldType;

class Date extends FieldType {

	public function generateField($name, $data, $attributes = array())
	{
		return '<input name="'.$name.'" value="'.$data.'"  id="'.$name.'"'.$attributes.'type="date" />';
	}


}