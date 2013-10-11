<?php namespace Packettide\Bree;

class FieldType {

	public $name;
	public $data;
	public $options;
	public $extra;
	protected $reservedOptions = array('label' => '', 'name' => '', 'type' => '');
	protected static $assets = array();

	public function __construct($name, $data, $options=array())
	{
		$this->name = $name;
		$this->data = $data;
		$this->options = $options;
		$this->extra = array_diff_key($options, $this->reservedOptions);
	}

	public function field($extra = array()) {}

	public function label($extra = array()) {
		$attrs = "";
		foreach ($extra as $key => $value) {
			$attrs .= "$key=\"$value\"";
		}
		if(isset($this->options['label']))
		{
			return '<label for="'.$this->name.'" '.$attrs.' >'.$this->options['label'].'</label>';
		}
	}

	public function save() {}

	public static function assets() {
		return static::$assets;
	}

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