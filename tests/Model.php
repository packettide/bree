<?php

use Mockery as m;
use Packettide\Bree\Admin\Model as AdminModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as EloquentModel;


class ModelTest extends PHPUnit_Framework_TestCase {

	public function setUp()
	{
		$this->fields = array('test' => array('type' => 'test'));
	}

	public function testBaseModelSet()
	{
		$baseModel = m::mock('Illuminate\Database\Eloquent\Model');
		$model = new AdminModel($baseModel, $this->fields);

		$this->assertInstanceOf('Illuminate\Database\Eloquent\Model', $model->baseModel);
	}

	public function testBaseModelCalls()
	{
		$baseModel = m::mock('Illuminate\Database\Eloquent\Model');
		$returnModel = m::mock('Illuminate\Database\Eloquent\Model');
		$queryBuilder = m::mock('Illuminate\Database\Eloquent\Builder');

		$queryBuilder->shouldReceive('find')->once()->with(1)->andReturn($returnModel);
		$baseModel->shouldReceive('newQuery')->once()->andReturn($queryBuilder);

		$model = new AdminModel($baseModel, $this->fields);

		$model->find(1);
		//$this->assertInstanceOf('Illuminate\Database\Eloquent\Model', $model->find(1));
		$this->assertSame($returnModel, $model->baseModelInstance);
	}

	public function testFieldGet()
	{
		$baseModel = m::mock('Illuminate\Database\Eloquent\Model');
		$returnModel = m::mock('Illuminate\Database\Eloquent\Model');
		$queryBuilder = m::mock('Illuminate\Database\Eloquent\Builder');

		$queryBuilder->shouldReceive('find')->once()->with(1)->andReturn($returnModel);
		$baseModel->shouldReceive('newQuery')->once()->andReturn($queryBuilder);
		$returnModel->shouldReceive('getAttribute')->once()->with('test');

		$fieldType = m::mock('Packettide\Bree\Admin\FieldType');
		$fieldType->shouldReceive('__toString')->once()->andReturn('<fieldtype>');

		$this->fields['test']['type'] = $fieldType;

		$model = new AdminModel($baseModel, $this->fields);

		$model->find(1);
		$this->assertEquals('<fieldtype>', $model->test);
	}

	public function testFieldSet()
	{
		$baseModel = m::mock('Illuminate\Database\Eloquent\Model');
		$returnModel = m::mock('Illuminate\Database\Eloquent\Model');
		$queryBuilder = m::mock('Illuminate\Database\Eloquent\Builder');

		$queryBuilder->shouldReceive('find')->once()->with(1)->andReturn($returnModel);
		$baseModel->shouldReceive('newQuery')->once()->andReturn($queryBuilder);
		//$returnModel->shouldReceive('getAttribute')->once()->with('test')->andReturn('testValue');
		$returnModel->shouldReceive('setAttribute')->once()->with('test', 'testValue');

		$fieldType = m::mock('Packettide\Bree\Admin\FieldType');
		$fieldType->shouldReceive('save')->once()->andReturn('');
		$fieldType->data = 'testValue';

		$this->fields['test']['type'] = $fieldType;

		$model = new AdminModel($baseModel, $this->fields);

		$model->find(1);
		$model->test = 'testValue';

		//$this->assertEquals('testValue', $model->test);
	}

}