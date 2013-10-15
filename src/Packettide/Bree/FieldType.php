<?php namespace Packettide\Bree;

class FieldType {

	public $name;
	public $data;
	public $options;

	public function __construct($name, $data, $options=array())
	{
		$this->name = $name;
		$this->data = $data;
		$this->options = $options;
	}

	public function field() {}

	public function label() {
		if(isset($this->options['label']))
		{
			return '<label for="'.$this->name.'">'.$this->options['label'].'</label>';
		}
	}

	public function save() {}

	public function __get($key) {
		if(isset($this->options[$key])) 
		{
			return $this->options[$key];
		}
	}

	public function __set($key, $value)
	{
		$this->options[$key] = $value;
	}

	public function __toString()
	{
		return $this->label() . $this->field();
	}



}