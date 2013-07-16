<?php namespace Packettide\Bree\Admin;

use Packettide\Bree\Admin\FieldType;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model {

	public $baseModel;
	public $baseModelInstance;
	public $fields;


	public function __construct($model, array $fields)
	{
		$this->setBaseModel($model);
		$this->fields = $fields;
	}


	/**
	 * Setup our Eloquent Base Model
	 * @param string|Illuminate\Database\Eloquent\Model $model
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
	 * Retrieve and setup the FieldType
	 * @param  string $key
	 * @param  string $data
	 * @param  array $field
	 * @return Packettide\Bree\Admin\FieldType
	 */
	public function getField($key, $data, $field)
	{
		if($field['type'] instanceof FieldType)
		{
			$data = $field['type'];
		}
		else
		{
			//could this be done with some kind of autoloading and exclude the namespace?
			$fieldType = 'Packettide\Bree\Admin\FieldTypes\\'.$field['type'];
			
			if(class_exists($fieldType))
			{
				$data = new $fieldType($key, $data, $field);

				if($this->isRelationField($data))
				{
					$data->relation = $this->fetchRelation($key);
				}
			}
		}
		return $data;
	}


	/**
	 * Check to see if the given fieldtype is a relation
	 * @param  Packettide\Bree\Admin\FieldType  $fieldtype
	 * @return boolean
	 */
	protected function isRelationField($fieldtype)
	{
		return ($fieldtype instanceof FieldTypeRelation) ? true : false;
	}


	/**
	 * Helper for retrieving relationship object
	 * @param  string $key
	 * @return Illuminate\Database\Eloquent\Relations\Relation
	 */
	protected function fetchRelation($key)
	{
		$relation = null;
		$camelKey = camel_case($key);
		
		if (method_exists($this->baseModel, $camelKey))
		{
			$relation = $this->baseModelInstance->$camelKey();
		}
		return $relation;
	}


	/*
	 * Save any post changes and echo out the form
	 */
	public function saveAndDisplay() {
		if (!empty($_POST)) {
			foreach ($this->fields as $key => $value) {
				// $value is not used
				$this->$key = isset($_POST[$key])? $_POST[$key] : $_FILES[$key];
			}
		}
		$this->save();
		echo $this;
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

		// Pass Eloquent static methods through, unfortunately they can't be called in the static context here.
		if (in_array($method, array('all', 'create', 'query', 'with', 'withTrashed', 'onlyTrashed', 'unguard', 'reguard', 'destroy')))
		{
			$instance = new $this->baseModel;
			return call_user_func_array(array($instance, $method), $parameters);
		}

		//echo 'here '. $method;

		$query = $this->baseModel->newQuery();

		// this returns an instance of baseModel
		$this->baseModelInstance = call_user_func_array(array($query, $method), $parameters);

		return $this;
	}


	/**
	 * Get field with attribute data
	 * @param  string $key
	 * @return string
	 */
	public function __get($key)
	{
		$attribute = $this->baseModelInstance->getAttribute($key);
		//var_dump($attribute[0]);
		//var_dump($this->fields[$key]);
		// if a FieldType was passed for this attribute process it
		if(isset($this->fields[$key]))
		{
			$attribute = $this->getField($key, $attribute, $this->fields[$key]);
		}
		return $attribute;
	}


	/**
	 * Set a field's value
	 * @param string $key
	 * @param string|array|object $value
	 */
	public function __set($key, $value)
	{
		$ft = $this->getField($key, $value, $this->fields[$key]);
		$ft->save();

		if(!$this->isRelationField($ft))
		{
			$this->baseModelInstance->setAttribute($key, $ft->data);
		}
	}


	/**
	 * Retrieve all fields
	 * @return string
	 */
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