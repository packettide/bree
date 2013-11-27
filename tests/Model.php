<?php

use Mockery as m;
use Packettide\Bree\Model as BreeModel;
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
		$baseModel->shouldReceive('hasGetMutator')->andReturn(false);

		$model = new BreeModel($baseModel, $this->fields);

		$this->assertInstanceOf('Illuminate\Database\Eloquent\Model', $model->baseModel);
	}

	public function testModelIsNew()
	{
		$baseModel = m::mock('Illuminate\Database\Eloquent\Model');
		$baseModel->shouldReceive('getAttribute')->once()->andReturn(null);
		$baseModel->shouldReceive('hasGetMutator')->andReturn(false);

		$model = new BreeModel($baseModel, $this->fields);

		$this->assertEquals(true, $model->isNew());
	}

	public function testBaseModelCalls()
	{
		$baseModel = m::mock('Illuminate\Database\Eloquent\Model');
		$returnModel = m::mock('Illuminate\Database\Eloquent\Model');
		$queryBuilder = m::mock('Illuminate\Database\Eloquent\Builder');

		$queryBuilder->shouldReceive('find')->once()->with(1)->andReturn($returnModel);
		$baseModel->shouldReceive('newQuery')->once()->andReturn($queryBuilder);
		$baseModel->shouldReceive('hasGetMutator')->andReturn(false);

		$model = new BreeModel($baseModel, $this->fields);

		$model->find(1);
		//$this->assertInstanceOf('Illuminate\Database\Eloquent\Model', $model->find(1));
		$this->assertSame($returnModel, $model->baseModelInstance);
	}

	public function testFieldGetValue()
	{
		$baseModel = m::mock('Illuminate\Database\Eloquent\Model');
		$returnModel = m::mock('Illuminate\Database\Eloquent\Model');
		$queryBuilder = m::mock('Illuminate\Database\Eloquent\Builder');

		$queryBuilder->shouldReceive('find')->once()->with(1)->andReturn($returnModel);
		$baseModel->shouldReceive('newQuery')->once()->andReturn($queryBuilder);
		$baseModel->shouldReceive('hasGetMutator')->andReturn(false);
		$returnModel->shouldReceive('getAttribute')->once()->with('test');

		$fieldType = m::mock('Packettide\Bree\FieldType');
		$fieldType->shouldReceive('__toString')->once()->andReturn('<fieldtype>');
		$fieldType->shouldReceive('withEvents')->once();

		$this->fields['test']['type'] = $fieldType;

		$model = new BreeModel($baseModel, $this->fields);

		$model->find(1);
		$this->assertEquals('<fieldtype>', $model->test);
	}

	public function testFieldSetValue()
	{
		$baseModel = m::mock('Illuminate\Database\Eloquent\Model');
		$returnModel = m::mock('Illuminate\Database\Eloquent\Model');
		$queryBuilder = m::mock('Illuminate\Database\Eloquent\Builder');

		$queryBuilder->shouldReceive('find')->once()->with(1)->andReturn($returnModel);
		$baseModel->shouldReceive('newQuery')->once()->andReturn($queryBuilder);
		$baseModel->shouldReceive('hasGetMutator')->andReturn(false);
		$returnModel->shouldReceive('getAttribute')->once()->with('id')->andReturn('1');
		$returnModel->shouldReceive('setAttribute')->once()->with('test', 'testValue');

		$fieldType = m::mock('Packettide\Bree\FieldType');
		$fieldType->shouldReceive('save')->once()->andReturn('')->andSet('data', 'testValue');
		$fieldType->shouldReceive('withEvents')->once();

		$this->fields['test']['type'] = $fieldType;

		$model = new BreeModel($baseModel, $this->fields);

		$model->find(1);
		$model->test = 'testValue';

		//$this->assertEquals('testValue', $model->test);
	}

}