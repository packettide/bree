<?php namespace Packettide\Bree\Admin\FieldTypes;

use Packettide\Bree\Admin\FieldTypeRelation;
use Illuminate\Support\Collection as Collection;
use Illuminate\Database\Eloquent\Relations;

class InlineStacked extends FieldTypeRelation {

	/**
	 *
	 */
	public function field()
	{

		if($this->relation instanceof Relations\HasMany)
		{

		}
		else
		{

		}
		$options = '';
		if($this->data instanceof Collection)
		{
			foreach($this->data as $row)
			{
				$options .= '<option value="'. $row->getKey() .'">'. $row->{$this->options['title']} .'</option>';
			}
		}
		else
		{

		}

		return '<select name="'.$this->name.'">'. $options .'</select>';
	}

	/**
	 *
	 */
	public function save()
	{
		/* If we have an id let's grab the model instance, otherwise assume we were given it */
		if(is_numeric($this->data))
		{
			$this->data = $this->related->find($this->data);
		}

		$this->relation->save($this->data);
	}


}