<?php namespace Packettide\Bree;

class FieldType {

	public $name;
	public $data;
	public $options;
	public $attributes;

	protected $reserved = array('label' => '', 'name' => '', 'type' => '', 'relation' => '', '_bree_field_class' => '');

	protected static $assets = array();

	public function __construct($name, $data, $options=array())
	{
		$this->name = $name;
		$this->data = $data;

		// Treat 'class' attribute as an array
		if(isset($options['class']))
		{
			$options['class'] = explode(' ', $options['class']);
		}

		$this->options = $options;
		// var_dump($this->options);
		$this->attributes = $this->removeReservedAttributes($options);
		// var_dump($this->attributes);
	}

	public function field($attributes = array()) {}

	public function label($attributes = array()) {
		$attrs = array();
		foreach ($attributes as $key => $value) {
			$attrs[] = $this->makeAttribute($key, $value);
		}
		$attrs = (count($attrs)) ? ' '.implode(' ', $attrs).' ' : '';

		if(isset($this->options['label']))
		{
			return '<label for="'.$this->name.'"'.$attrs.'>'.$this->options['label'].'</label>';
		}
	}

	public function save() {}

	protected function removeReservedAttributes($attributes = array())
	{
		return array_diff_key($attributes, $this->reserved);
	}

	protected function getFieldAttributes($attributes = array())
	{
		$attributes = array_merge($attributes, $this->attributes);

		// need to remove any reserved attributes here
		$attributes = $this->removeReservedAttributes($attributes, $this->reserved);
		$attrs = array();

		if($attributes)
		{
			foreach ($attributes as $key => $value) {
				if(is_array($value))
				{
					$attrs[] = $this->makeAttribute($key, implode(' ', $value));
				}
				else{
					$attrs[] = $this->makeAttribute($key, $value);
				}
			}
		}

		// we might as well export reserved attributes here
		// would be great to have "hidden" attributes as well (ex: "_bree_field_class")

		// pad the attributes string
		return (count($attrs)) ? ' '.implode(' ', $attrs).' ' : '';
	}

	private function makeAttribute($key, $value)
	{
		if (!is_null($value))
			return $key.'="'.e($value).'"';
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