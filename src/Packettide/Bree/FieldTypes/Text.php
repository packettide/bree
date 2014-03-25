<?php namespace Packettide\Bree\FieldTypes;

use Packettide\Bree\FieldType;

class Text extends FieldType {

	public function generateField($name, $data, $attributes = array())
	{
		return '<input name="'.$name.'" value="'.htmlentities($data).'"  id="'.$name.'"'.$attributes.'/>';
	}


}