<?php namespace Packettide\Bree\Admin;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model {

	public $baseModel;
	public $baseModelInstance;
	public $fields;


	public function __construct($model, array $fields)
	{
		$this->setBaseModel($model);
		$this->fields = $fields;

		//var_dump($this->baseModel);
		//var_dump($this->fields);
	}


	/**
	 * Setup our Eloquent Base Model
	 */
	public function setBaseModel($model)
	{
		if($model instanceof EloquentModel)
		{
			$this->baseModel = $model;
		}
		else if(class_exists($model))
		{
			$this->baseModel = new $model;
		}
		else
		{
			//abort error
		}
	}

	/**
	 *
	 */
	public function getField($key, $data, $fieldType)
	{
		//could this be done with some kind of autoloading and exclude the namespace?
		$fieldType = 'Packettide\Bree\Admin\FieldTypes\\'.$fieldType;
		if(class_exists($fieldType))
		{
			$data = new $fieldType($key, $data);
		}
		return $data;
	}



	/**
	 * Handle dynamic method calls into the method.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		if (in_array($method, array('save','increment', 'decrement')))
		{
			return call_user_func_array(array($this->baseModelInstance, $method), $parameters);
		}

		//echo 'here '. $method;

		$query = $this->baseModel->newQuery();

		// this returns an instance of baseModel
		$this->baseModelInstance =  call_user_func_array(array($query, $method), $parameters);

		return $this;
	}

	/**
	 * Handle dynamic static method calls into the method.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public static function __callStatic($method, $parameters)
	{
		$instance = new $this->baseModel;

		return call_user_func_array(array($instance, $method), $parameters);
	}


	public function __get($key)
	{
		$attribute = $this->baseModelInstance->getAttribute($key);
		var_dump($attribute[0]);
		
		// if a FieldType was passed for this attribute process it
		if(isset($this->fields[$key]))
		{
			$attribute = $this->getField($key, $attribute, $this->fields[$key]);
		}
		return $attribute;
	}


	public function __set($key, $value)
	{
		$this->baseModelInstance->setAttribute($key, $value);
	}

	public function __toString()
	{
		$output = '';

		foreach($this->fields as $field => $type)
		{
			$output .= '<div class="field">'. $this->$field . '</div>';
		}
		
		return $output;
		//return $this->baseModelInstance->toJson();
	}

}