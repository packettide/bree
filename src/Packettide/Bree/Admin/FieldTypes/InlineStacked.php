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
				$selected = ($chosen->find($row->getKey())) ? 'selected' : '';
				$options .= '<option '. $selected .' value="'. $row->getKey() .'">'. $row->{$this->options['title']} .'</option>';
			}
		}
		else
		{

		}

		if($this->relation instanceof Relations\HasMany)
		{
			return '<select multiple name="'.$this->name.'">'. $options .'</select>';
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
		if($this->relation instanceof Relations\HasMany)
		{

		}
		else
		{

		}

		/* If we have an id let's grab the model instance, otherwise assume we were given it */
		if(is_numeric($this->data))
		{
			$this->data = $this->related->find($this->data);
		}

		$this->relation->save($this->data);
	}


}