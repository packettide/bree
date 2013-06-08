<?php namespace Packettide\Bree\Admin\FieldTypes;

use Packettide\Bree\Admin\FieldType;

class InlineStacked extends FieldType {

	/**
	 *
	 */
	public function field()
	{
		// return Form::text($name, $data);
		if(is_array($this->data))
		{

		}
		else
		{

		}
		return '<input name="'.$this->name.'" value="'.$this->data.'" />';
	}

	/**
	 *
	 */
	public function save($relation)
	{
		if(is_array($this->data))
		{
			$relation->saveMany($this->data);
		}
		else
		{
			echo 'here1';
			$relation->save($this->data);
		}
	}


}