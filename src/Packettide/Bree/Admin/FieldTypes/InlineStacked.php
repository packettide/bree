<?php namespace Packettide\Bree\Admin\FieldTypes;

use Packettide\Bree\Admin\FieldTypeRelation;
use Illuminate\Database\Eloquent\Collection as Collection;
use Illuminate\Database\Eloquent\Relations;

class InlineStacked extends FieldTypeRelation {

	/**
	 *
	 */
	public function field()
	{
		$available = $this->related->all();
		$chosen = $this->data;
		$options = '';

		if($available instanceof Collection)
		{
			foreach($available as $row)
			{
				$selected = '';

				if($this->relation instanceof Relations\HasMany) 
				{
					$selected = ($chosen->contains($row->getKey())) ? 'selected ' : '';
				}
				else if($row->getKey() == $chosen->getKey()) 
				{
					$selected = 'selected ';
				}

				$options .= '<option '. $selected .'value="'. $row->getKey() .'">'. $row->{$this->options['title']} .'</option>';
			}
		}

		if($this->relation instanceof Relations\HasMany)
		{
			return '<select multiple name="'.$this->name.'[]">'. $options .'</select>';
		}
		else
		{
			return '<select name="'.$this->name.'">'. $options .'</select>';
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
				foreach($this->data as $key => $item)
				{
					$this->data[$key] = $this->related->baseModel->find($item);
				}
			}

			$this->relation->saveMany($this->data);
		}
		else
		{
			/* If we have an id let's grab the model instance, otherwise assume we were given it */
			$this->data = (is_numeric($this->data)) ? $this->related->baseModel->find($this->data) : $this->data;

			parent::saveRelation($this->relation, $this->data);

		} 

	}


}