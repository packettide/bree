<?php namespace Packettide\Bree\FieldSets;

use Packettide\Bree\FieldSet;

class AdvancedFieldSet extends FieldSet {

	public $assets = array(
			'packettide/bree/jquery.min.js',
			'packettide/bree/handlebars.js'
		);

	public $name = 'advanced';

}