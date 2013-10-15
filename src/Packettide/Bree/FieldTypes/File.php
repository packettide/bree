<?php namespace Packettide\Bree\FieldTypes;

use Packettide\Bree\FieldType;

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
		
		$fileLocation = ($this->directory != '') ? $this->directory : '';

		$fileLocation .= $this->data['name'];

		if(move_uploaded_file($this->data['tmp_name'], $fileLocation))
		{
			$this->data = str_replace(public_path(), '', $fileLocation);
		}
		else
		{
			// error
		}

	}


}