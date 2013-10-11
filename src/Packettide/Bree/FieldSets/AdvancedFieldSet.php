<?php namespace Packettide\Bree\FieldSets;

use Packettide\Bree\FieldSet;

class AdvancedFieldSet extends FieldSet {

	public $assets = array(
			'packages/packettide/bree/jquery.min.js',
			'packages/packettide/bree/handlebars.js'
		);

	public $name = 'advanced';

}