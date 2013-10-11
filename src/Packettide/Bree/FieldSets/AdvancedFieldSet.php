<?php namespace Packettide\Bree\FieldSets;

use Packettide\Bree\FieldSet;

class AdvancedFieldSet extends FieldSet {

	public static $assets = array(
			'jquery.min.js',
			'handlebars.js'
		);

	public static function getName()
	{
		return 'advanced';
	}

}