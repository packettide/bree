<?php namespace Packettide\Bree\FieldTypes;

use Packettide\Bree\FieldType;

class TextArea extends FieldType {

	public function generateField($name, $data, $attributes = array())
	{
		return '<textarea name="'.$name.'" id="'.$name.'"'.$attributes.'>'.$data.'</textarea>';
	}


}