<?php namespace Packettide\Bree\FieldTypes;

use Packettide\Bree\FieldType;

class Text extends FieldType {

	public function field()
	{
		// return Form::text($name, $data);
		return '<input name="'.$this->name.'" value="'.$this->data.'" />';
	}


}