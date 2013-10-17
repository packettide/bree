<?php namespace Packettide\Bree\FieldTypes;

use Packettide\Bree\FieldType;

class Time extends FieldType {

	public function field($extra = array())
	{
		$extra = array_merge($extra, $this->extra);
		$attrs = "";
		foreach ($extra as $key => $value) {
			$attrs .= "$key=\"$value\"";
		}
		return '<input name="'.$this->name.'" value="'.$this->data.'"  id="'.$this->name.'" '.$attrs.' type="time" />';
	}


}