<?php

use Packettide\Bree\Admin\FieldType;

class FieldTypeTest extends PHPUnit_Framework_TestCase {

	public function setUp()
	{
		$name = 'fieldtest';
		$data = 'data';
		$options = array('title' => 'name', 'label' => 'Field Test');
		$this->fieldType = new FieldType($name, $data, $options);
	}

	public function testLabel()
	{
		$this->assertEquals('<label for="fieldtest">Field Test</label>', $this->fieldType->label());
	}

}