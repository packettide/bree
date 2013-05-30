<?php namespace Packettide\Bree\Admin\FieldTypes;

use Packettide\Bree\Admin\FieldType;

class TextArea extends FieldType {

	public static function get($name, $data)
	{
		// return Form::text($name, $data);
		return '<textarea name="'.$name.'">'.$data.'</textarea>';
	}


}