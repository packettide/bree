<?php namespace Packettide\Bree\Admin\FieldTypes;

use Packettide\Bree\Admin\FieldType;

class File extends FieldType {

	public function field()
	{
		// return Form::text($name, $data);
		return '<input type="file" name="'.$this->name.'" />';
	}

	/**
	 *
	 */
	public function save()
	{
		if(empty($this->data)) return;

		move_uploaded_file($this->data['tmp_name'], 'test.jpeg');

		$this->data = $this->data['tmp_name'];

	}


}