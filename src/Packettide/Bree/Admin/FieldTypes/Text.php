<?php namespace Packettide\Bree\Admin\FieldTypes;

use Packettide\Bree\Admin\FieldType;

class Text extends FieldType {

	public static function get($name, $data)
	{
		// return Form::text($name, $data);
		return '<input name="'.$name.'" value="'.$data.'" />';
	}


}