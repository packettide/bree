<?php namespace Packettide\Bree\Admin\FieldTypes;

use Packettide\Bree\Admin\FieldType;

class TextArea extends FieldType {

	public function field()
	{
		return '<textarea name="'.$this->name.'">'.$this->data.'</textarea>';
	}


}