<?php namespace Packettide\Bree\FieldTypes;

use Packettide\Bree\FieldType;

class Time extends FieldType {

	public function field($attributes = array())
	{
		$attrs = $this->getFieldAttributes($attributes);
		return '<input name="'.$this->name.'" value="'.$this->data.'"  id="'.$this->name.'" '.$attrs.' type="time" />';
	}


}