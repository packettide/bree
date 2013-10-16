<?php namespace Packettide\Bree\FieldTypes;

use Packettide\Bree\FieldType;

class TextArea extends FieldType {

	public function field($extra = array())
	{
		$extra = array_merge($extra, $this->extra);
		$attrs = "";
		foreach ($extra as $key => $value) {
			$attrs .= "$key=\"$value\"";
		}
		return '<textarea name="'.$this->name.'" id="'.$this->name.'" '.$attrs.' >'.$this->data.'</textarea>';
	}


}