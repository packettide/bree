<?php namespace Packettide\Bree\FieldTypes;

use Packettide\Bree\FieldType;

class Time extends FieldType {

	public function generateField($name, $data, $attributes = array())
	{
		return '<input name="'.$name.'" value="'.$data.'"  id="'.$name.'" '.$attributes.' type="time" />';
	}


}