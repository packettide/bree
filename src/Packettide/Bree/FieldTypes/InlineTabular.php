<?php namespace Packettide\Bree\FieldTypes;

use Packettide\Bree\FieldTypeRelation;
use Illuminate\Support\Collection as Collection;

class InlineTabular extends FieldTypeRelation {

	/**
	 *
	 */
	public function field($extra = array())
	{
		$extra = array_merge($extra, $this->extra);
		$attrs = "";
		foreach ($extra as $key => $value) {
			$attrs .= "$key=\"$value\"";
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
	public function save($relation)
	{
		if(is_array($this->data))
		{
			$relation->saveMany($this->data);
		}
		else
		{
			echo 'here1';
			$relation->save($this->data);
		}
	}


}