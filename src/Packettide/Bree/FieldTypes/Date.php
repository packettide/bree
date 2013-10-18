<?php namespace Packettide\Bree\FieldTypes;

use Packettide\Bree\FieldType;

class Date extends FieldType {

	public function field($extra = array())
	{
		$attrs = $this->getFieldAttributes($extra);
		return '<input name="'.$this->name.'" value="'.$this->data.'"  id="'.$this->name.'"'.$attrs.'type="date" />';
	}


}