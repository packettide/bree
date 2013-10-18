<?php namespace Packettide\Bree;

class FieldType {

	public $name;
	public $data;
	public $options;
	public $extra;
	protected $reservedOptions = array('label' => '', 'name' => '', 'type' => '', 'relation' => '', '_bree_field_class' => '');
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

	protected function getFieldAttributes($extra = array())
	{
		$extra = array_merge($extra, $this->extra);

		// need to remove any reserved attributes here
		$extra = array_diff_key($extra, $this->reservedOptions);
		$attrs = "";

		if($extra)
		{
			foreach ($extra as $key => $value) {
				$attrs .= "$key=\"$value\"";
			}
		}

		// we might as well export reserved attributes here
		// would be great to have "hidden" attributes as well (ex: "_bree_field_class")

		// pad the attributes string
		if($attrs) $attrs = ' '.$attrs.' ';

		return $attrs;
	}

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