<?php namespace Packettide\Bree\FieldTypes;

use Packettide\Bree\FieldTypeRelation;
use Illuminate\Database\Eloquent\Collection as Collection;
use Illuminate\Database\Eloquent\Relations;

class Relate extends FieldTypeRelation {

	/**
	 *
	 */
	public function generateField($name, $data, $attrs)
	{
		$available = $this->related->all();
		$chosen = $this->data;
		$options = '';


		if($available instanceof Collection)
		{
			foreach($available as $row)
			{
				$selected = false;

				if($this->hasMultiple())
				{
					if($chosen->contains($row->getKey()))
					{
						$selected = true;
					}
				}
				else if($chosen && $row->getKey() == $chosen->getKey()) //consolidate
				{
					$selected = true;
				}

				$options .= $this->generateOption($row->getKey(), $row->{$this->options['title']}, $selected);
			}
		}

		if($this->select === false)
		{
			return $options;
		}

		if($this->hasMultiple())
		{
			return '<select multiple name="'.$name.'[]" id="'.$name.'"'.$attrs.'>'. $options .'</select>';
		}
		else
		{
			return '<select name="'.$name.'" id="'.$name.'"'.$attrs.'>'. $options .'</select>';
		}

	}

	public function generateOption($value, $label, $selected)
	{
		if($this->select === false)
		{
			$selected = ($selected) ? 'checked="checked" ' : '';
			$type = ($this->hasMultiple()) ? 'checkbox' : 'radio';
			$name = ($this->hasMultiple()) ? $this->name .'[]' : $this->name;

			return '<input '. $selected .'type="'. $type .'" name="'. $name .'" value="'. $value . '">'. $label.'<br>';
		}
		else
		{
			$selected = ($selected) ? 'selected ' : '';
			return '<option '. $selected .'value="'. $value .'">'. $label .'</option>';
		}
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
				$data = new Collection();

				foreach($this->data as $key => $item)
				{
					// $this->data[$key] = $this->related->baseModel->find($item);
					$data->push($this->related->baseModel->find($item));
				}
			}
			else
			{
				$data = new Collection($this->data);
			}

			// detach any existing models and only save the selected ones
			$foreignKey = $this->relation->getPlainForeignKey();
			$current = $this->relation->getResults();

			if(!$current)
			{
				$this->relation->saveMany($data->toArray());
				return;
			}

			$all = $data->merge($current);

			foreach($all as $item)
			{
				if($keep = $data->find($item->getKey()))
				{
					$this->relation->save($keep);
				}
				else
				{
					$item->$foreignKey = null;
					$item->save();
				}
			}
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