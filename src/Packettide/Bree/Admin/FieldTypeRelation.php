<?php namespace Packettide\Bree\Admin;

use Illuminate\Database\Eloquent\Relations;

class FieldTypeRelation extends FieldType {

	public function __construct($name, $data, $options=array())
	{
		parent::__construct($name, $data, $options);
	}

	public static function saveRelation($relation, $data)
	{
		if($relation instanceof Relations\HasOne)
		{
			$relation->save($data);
		}
		else if($relation instanceof Relations\BelongsTo)
		{
			$relation->associate($data);
		}
	}

}