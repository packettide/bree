<?php namespace Packettide\Bree\FieldTypes;

use Packettide\Bree\FieldType;

class TextArea extends FieldType {

	public function field()
	{
		return '<textarea name="'.$this->name.'">'.$this->data.'</textarea>';
	}


}