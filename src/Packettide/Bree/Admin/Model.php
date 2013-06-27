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
	public function getField($key, $data, $field)
	{
		if($field['type'])
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

	/*
	 * Check to see if the given fieldtype is a relation
	 */
	protected function isRelationField($fieldtype)
	{
		return ($fieldtype instanceof FieldTypeRelation) ? true : false;
	}

	/*
	 * Helper for retrieving relationship object
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
		$this->baseModelInstance =  call_user_func_array(array($query, $method), $parameters);

		return $this;
	}

	// WILL NOT WORK
	// /**
	//  * Handle dynamic static method calls into the method.
	//  *
	//  * @param  string  $method
	//  * @param  array   $parameters
	//  * @return mixed
	//  */
	// public static function __callStatic($method, $parameters)
	// {
	// 	$instance = $this->baseModel;

	// 	return call_user_func_array(array($instance, $method), $parameters);
	// }


	public function __get($key)
	{
		$attribute = $this->baseModelInstance->getAttribute($key);
		//var_dump($attribute[0]);
		
		// if a FieldType was passed for this attribute process it
		if(isset($this->fields[$key]))
		{
			$attribute = $this->getField($key, $attribute, $this->fields[$key]);
		}
		return $attribute;
	}


	public function __set($key, $value)
	{
		if(isset($this->fields[$key]))
		{
			$ft = $this->getField($key, $value, $this->fields[$key]);
		
			$ft->save();			
		}
		else
		{
			$this->baseModelInstance->setAttribute($key, $value);	
		}
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