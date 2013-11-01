<?php namespace Packettide\Bree;

class FieldType {

	public $name;
	public $data;
	public $options;
	public $attributes;

	protected $reserved = array('label', 'name', 'type', 'fieldset', '_bree_field_class');
	protected static $assets = array();

	private $prefix = "";

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
		$this->attributes = $this->removeReservedAttributes($options);
	}

	public function withEvents($events)
	{
		$this->events = $events;
	}

	public function field($attributes = array()) {
		$attrs = $this->getFieldAttributes($attributes);
		list($name, $data, $attrs) = $this->renderHook($attrs);
		return $this->generateField($name, $data, $attrs);
	}

	public function save() {}

	/**
	 * Generate an HTML label for the field
	 * @param  array  $attributes additional attributes for the label
	 * @return string
	 */
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

	/**
	 * Filter out reserved attributes from an array of attributes
	 * @param  array  $attributes
	 * @return array
	 */
	protected function removeReservedAttributes($attributes = array())
	{
		return array_diff_key($attributes, array_flip($this->reserved));
	}

	/**
	 * Create a proper string of attributes
	 * @param  array  $attributes
	 * @return string
	 */
	protected function getFieldAttributes($attributes = array())
	{
		$attributes = array_merge($attributes, $this->attributes);

		// need to remove any reserved attributes here
		$attributes = $this->removeReservedAttributes($attributes, $this->reserved);
		$attrs = array();

		if($attributes)
		{
			foreach ($attributes as $key => $value) {
				$attrs[] = $this->makeAttribute($key, $value);
			}
		}

		// pad the attributes string
		return (count($attrs)) ? ' '.implode(' ', $attrs).' ' : '';
	}

	/**
	 * Turn key-value pair into an HTML attribute
	 * @param  string 		$key
	 * @param  string|array $value
	 * @return string
	 */
	private function makeAttribute($key, $value)
	{
		if(is_array($value))
			$value = implode(' ', $value);

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

	public function renderHook($attrs)
	{
		if (isset($this->events['field.render']))
		{
			 return $this->events['field.render']($this->name, $this->data, $attrs);
		}
		else
		{
			return array($this->name, $this->data, $attrs);
		}
	}

	public function getPrefix()
	{
		return $this->prefix;
	}


}