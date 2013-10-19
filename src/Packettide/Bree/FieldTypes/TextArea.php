<?php namespace Packettide\Bree\FieldTypes;

use Packettide\Bree\FieldType;

class TextArea extends FieldType {

	public function field($attributes = array())
	{
		$attrs = $this->getFieldAttributes($attributes);
		return '<textarea name="'.$this->name.'" id="'.$this->name.'"'.$attrs.'>'.$this->data.'</textarea>';
	}


}