<?php namespace Packettide\Bree\FieldTypes;

use Packettide\Bree\FieldTypeRelation;
use Illuminate\Database\Eloquent\Collection as Collection;
use Illuminate\Database\Eloquent\Relations;

class Matrix extends FieldTypeRelation {

	/**
	 *
	 */
	public function field($attributes = array())
	{
		$attrs = $this->getFieldAttributes($attributes);

		$available = $this->related->all();
		// $chosen = $this->data;
		$options = '';


		if($available instanceof Collection)
		{
			foreach($available as $row)
			{
				$options .= $this->generateRow($row);
			}
		}

		return "<table $attrs><thead>" . $this->generateHeaders($row) . "</thead><tbody>" . $options . "</tbody></table>";

		// if($this->select === false)
		// {
		// 	return $options;
		// }

		// if($this->hasMultiple())
		// {
		// 	return '<select multiple name="'.$this->name.'[]" id="'.$this->name.'"'.$attrs.'>'. $options .'</select>';
		// }
		// else
		// {
		// 	return '<select name="'.$this->name.'" id="'.$this->name.'"'.$attrs.'>'. $options .'</select>';
		// }

	}

	public function generateHeaders($row)
	{
		$toReturn = "<tr>";
		$admin = new \Packettide\Bree\Model($row);
		foreach ($admin->fields as $key => $value) {
			$toReturn .= '<th>';
			$toReturn .= $admin->$key->label();
			$toReturn .= '</th>';
		}
		return $toReturn . '</tr>';
	}

	public function generateRow($row)
	{
		$toReturn = "<tr>";
		$admin = new \Packettide\Bree\Model($row);
		$admin->attachObserver("field.render", function($name, $data, $attrs) {
			return ['mt_'.$this->name.'_'.$name.'[]', $data, $attrs];
		});
		foreach ($admin->fields as $key => $value) {
			$toReturn .= '<td>';
			$toReturn .= $admin->$key->field();
			$toReturn .= '</td>';
		}
		return $toReturn . '</tr>';
	}

	/**
	 *
	 */
	public function save()
	{
		if(empty($this->data)) return;

		if($this->relation instanceof Relations\HasMany && is_array($this->data))
		{
			if(is_numeric($this->data[0])) // assume we have an array of ids
			{
				foreach($this->data as $key => $item)
				{
					$this->data[$key] = $this->related->baseModel->find($item);
				}
			}

			$this->relation->saveMany($this->data);
		}
		else if($this->relation instanceof Relations\BelongsToMany && is_array($this->data))
		{
			if(is_numeric($this->data[0])) // assume we have an array of ids
			{
				$this->relation->sync($this->data);
			}
		}
		else
		{
			/* If we have an id let's grab the model instance, otherwise assume we were given it */
			$this->data = (is_numeric($this->data)) ? $this->related->baseModel->find($this->data) : $this->data;

			parent::saveRelation($this->relation, $this->data);

		}

	}


}