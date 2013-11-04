<?php namespace Packettide\Bree;

use Packettide\Bree\FieldType;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model {

	public $baseModel;
	public $baseModelInstance;
	public $fields;

	protected $fieldsets = array();
	protected $assets;

	protected $events = array();


	public function __construct($model, array $fields = array())
	{
		$this->setBaseModel($model);
		$this->setModelFields($fields);
		$this->baseModelInstance = $this->baseModel;
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
			throw new \Exception('Invalid Eloquent Model');
		}
	}

	public function setModelFields($fields)
	{
		if(isset($this->baseModel->breeFields) && is_array($this->baseModel->breeFields))
		{
			$fields = array_merge($this->baseModel->breeFields, $fields);
		}

		foreach($fields as &$field)
		{
			$field['_bree_field_class'] = $this->resolveFieldClass($field);
			// var_dump($field);
			// echo $this->resolveFieldClass($field['type']);
		}
		// var_dump($fields);

		$this->fields = $fields;
	}


	/**
	 * Retrieve and setup the FieldType
	 * @param  string $key
	 * @param  string $data
	 * @param  array $field
	 * @return Packettide\Bree\FieldType
	 */
	public function getField($key, $data, $field)
	{
		if($field['type'] instanceof FieldType)
        {
            $data = $field['type'];
        }
        else
        {
			// Check and see if this field has been preloaded
			// this should be the preferred method
			if(isset($field['_bree_field_class']))
			{
				$fieldType = $field['_bree_field_class'];
			}
			else
			{
				$fieldType = $this->resolveFieldClass($field);
			}

			$data = new $fieldType($key, $data, $field);

			if($this->isRelationField($data))
			{
				$data->relation = $this->fetchRelation($key);
			}
		}
		$data->withEvents($this->events);
		return $data;
	}

	public function resolveFieldClass($field)
	{
		$fieldType = FieldSetProvider::getFieldType($field['type']);

		// If a particular fieldset is desired we will look for it in
		// the list of available sets and remove all others if it exits
		if(isset($field['fieldset']))
		{
			$fieldSet = $field['fieldset'];

			if(array_key_exists($fieldSet, $fieldType))
			{
				$fieldType = array($fieldSet => $fieldType[$fieldSet]);
			}
			else
			{
				throw new \Exception('Fieldset "'.$fieldSet.'" not available');
			}
		}

		$fieldClass = $this->registerFieldType($fieldType);

		return $fieldClass;
	}

	/**
	 * Register $fieldtype with model and favor fieldtype implementations
	 * with popular fieldsets in the model
	 *
	 * @param  [type] $fieldType [description]
	 * @return [type]            [description]
	 */
	protected function registerFieldType($fieldType)
	{
		if(!is_array($fieldType)) return false;

		$fieldClass = '';

		$numFieldTypes = count($fieldType);

		if($numFieldTypes)
		{
			$cur = 1;

			foreach($fieldType as $fieldset => $field)
			{
				// If we don't have a fieldclass determined yet and the
				// fieldset for this current field is in use OR it's our
				// last option let's choose it
				if( !$fieldClass && (in_array($fieldset, $this->fieldsets) || $cur === $numFieldTypes) )
				{
					if(!in_array($fieldset, $this->fieldsets)){
						$this->fieldsets = array_merge($this->fieldsets, array($fieldset));
					}

					$fieldClass = $field;
				}

				$cur++;
			}
		}

		return $fieldClass;
	}


	/**
	 * Check to see if the given fieldtype is a relation
	 * @param  Packettide\Bree\FieldType  $fieldtype
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

	public function isNew()
	{
		return $this->baseModelInstance->id == null;
	}

	public function assets()
	{

		if(! $this->assets)
		{
			$assets = new Assets\Collection;

			foreach($this->fieldsets as $fieldset)
			{
				$fieldset = FieldSetProvider::get($fieldset);

				$assets->put(get_class($fieldset), $fieldset::assets());
				$assets = $assets->merge($fieldset->fieldTypeAssets());
			}

			$this->assets = $assets;
		}

		return $this->assets;
	}

	/**
	 * Generate model fields output
	 * @return string
	 */
	public function fields()
	{
		$output = '';

		foreach($this->fields as $field => $type)
		{
			$output .= '<div class="field">'. $this->$field . '</div>';
		}

		return $output;
	}

	/*
	 * Save any post changes and echo out the form
	 */
	public function saveAndDisplay()
	{
		if (!empty($_POST)) {
			foreach ($this->fields as $key => $value) {
				// $value is not used

				if(isset($_POST[$key]))
				{
					$this->$key = $_POST[$key];
				}
				else if(isset($_FILES[$key]) && !empty($_FILES[$key]['tmp_name']))
				{
					$this->$key = $_FILES[$key];
				}
			}
			$this->save();
		}
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
		if(strpos($key, 'cell_') === 0) {
			$key = substr($key, 5);
		}
		$ft = $this->getField($key, $value, $this->fields[$key]);

		if($this->isNew())
		{
			$tempModel = $this->baseModel->create(array());
			$tempModel->save();
			$this->find($tempModel->id);
		}

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
		return $this->assets() . $this->fields();
	}

	public function attachObserver($event, $fn)
	{
		$this->events[$event] = $fn;
	}

}