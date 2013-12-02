<?php namespace Packettide\Bree\FieldTypes;

use Packettide\Bree\FieldType;
use \Symfony\Component\HttpFoundation\File\UploadedFile;

class File extends FieldType {

	protected static $assets = array(
			'packettide/bree/file.js'
		);

	public function __construct($name, $data, $options=array())
	{
		// Add another reserved attribute for file fieldtype
		array_push($this->reserved, 'directory');

		parent::__construct($name, $data, $options);
	}

	public function generateField($name, $data, $attributes = array())
	{
		if($data)
		{
			$output = '<input type="hidden" name="'.$name.'" id="'.$name.'" value="'.$data.'"'.$attributes.'/>' .
					  '<a class="bree-file-view" target="_blank" href="'.$data.'">View File: '.$data.'</a> ' .
					  '<a class="bree-file-remove" data-field="'.$name.'" href="#">Remove File</a>';
		}
		else
		{
			$output = '<input type="file" name="'.$name.'" id="'.$name.'"'.$attributes.'/>';
		}

		return $output;
	}

	/**
	 *
	 */
	public function save()
	{
		if(empty($this->data)) return;

		$fileLocation = ($this->directory) ? (public_path() . $this->directory) : '';

		// If we have a string and it represents a saved filelocation do nothing
		if (is_string($this->data) && strpos($this->data, $this->removePublicPath($fileLocation)) !== false) return $this->data;

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
			$this->data = $this->removePublicPath($fileLocation.$fileName);
		}
		catch(\Exception $e)
		{
			$this->data = '';
		}
	}

	public function removePublicPath($path)
	{
		return str_replace(public_path(), '', $path);
	}


}