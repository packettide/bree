<?php namespace Packettide\Bree\FieldTypes;

use Packettide\Bree\FieldType;
use \Symfony\Component\HttpFoundation\File\UploadedFile;

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

		$fileLocation = ($this->directory) ? $this->directory : '';

		if(! $this->data instanceof UploadedFile)
		{
			if(is_array($this->data) && isset($this->data['name']) && isset($this->data['tmp_name']))
			{
				$this->data = new UploadedFile($this->data['tmp_name'], $this->data['name']);
			}
			else
			{
				throw new \Exception('Invalid File');
			}
		}

		$fileName = $this->data->getClientOriginalName();

		try
		{
			$file = $this->data->move($fileLocation, $fileName);
			$this->data = str_replace(public_path(), '', $fileLocation.$fileName);
		}
		catch(\Exception $e)
		{
			$this->data = '';
		}

	}


}