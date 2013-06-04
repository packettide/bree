<?php namespace Packettide\Bree\Admin\FieldTypes;

use Packettide\Bree\Admin\FieldType;

class Text extends FieldType {

	public function field()
	{
		// return Form::text($name, $data);
		return '<input name="'.$this->name.'" value="'.$this->data.'" />';
	}


}