<?php namespace Packettide\Bree\FieldTypes;

use Packettide\Bree\FieldType;

class TextArea extends FieldType {

	public function field($extra = array())
	{
		$attrs = $this->getFieldAttributes($extra);
		return '<textarea name="'.$this->name.'" id="'.$this->name.'"'.$attrs.'>'.$this->data.'</textarea>';
	}


}