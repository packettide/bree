<?php namespace Packettide\Bree;

use Illuminate\Database\Eloquent\Relations;

class FieldTypeRelation extends FieldType {

	public function __construct($name, $data, $options=array())
	{

		/**
		 * Make sure the related model is initialized
		 */
		if(isset($options['related']) && !($options['related'] instanceof Model))
		{
			$options['related'] = new Model($options['related']);
		}

		parent::__construct($name, $data, $options);
	}


	public static function saveRelation($relation, $data)
	{
		if($relation instanceof Relations\HasOne)
		{
			$relation->save($data);
		}
		else if($relation instanceof Relations\BelongsTo)
		{
			$relation->associate($data);
		}
		else if($relation instanceof Relations\BelongsToMany)
		{

		}
	}

	/**
	 * Check if this FieldTypeRelation can have multiple values
	 * @return boolean
	 */
	public function hasMultiple()
	{
		return ($this->relation instanceof Relations\HasMany || $this->relation instanceof Relations\BelongsToMany ) ? true : false;
	}


	/**
	 * Override parent getter to call getResults() if data hasn't been populated
	 * @param string $key
	 */
	public function __get($key)
	{
		if($key == 'data' && empty($this->options['data']) && isset($this->relation))
		{
			$this->options['data'] = $this->relation->getResults();
		}
		return parent::__get($key);
	}


}