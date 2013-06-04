<?php namespace Packettide\Bree\Admin;

class FieldType {

	public $name;
	public $data;

	public function __construct($name, $data, $options=array())
	{
		$this->name = $name;
		$this->data = $data;
		$this->options = $options;
	}

	public function field() {}

	

	public function __get($key) {
		if(isset($this->options[$key])) 
		{
			return $this->options[$key];
		}
	}

	public function __toString()
	{
		return $this->field();
	}



}