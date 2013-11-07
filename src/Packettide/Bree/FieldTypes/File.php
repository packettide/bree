<?php namespace Packettide\Bree\FieldTypes;

use Packettide\Bree\FieldType;

class File extends FieldType {

	public function __construct($name, $data, $options=array())
	{
		// Add another reserved attribute for file fieldtype
		array_push($this->reserved, 'directory');

		parent::__construct($name, $data, $options);
	}

	public function generateField($name, $data, $attributes = array())
	{
		$output = '<input type="file" name="'.$name.'" id="'.$name.'"'.$attributes.'/>';
		if($data) $output .= '<a target="_blank" href="'.$data.'">View File: '.$data.'</a>';

		return $output;
	}

	/**
	 *
	 */
	public function save()
	{
		if(empty($this->data)) return;

		$fileLocation = ($this->directory != '') ? $this->directory : '';
		// replace spaces
		$fileLocation .= $this->data['name'];
		if(move_uploaded_file($this->data['tmp_name'], $fileLocation))
		{
			$this->data = str_replace(public_path(), '', $fileLocation);
		}
		else
		{
			$this->data = '';
		}

	}


}