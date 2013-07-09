<?php

use Mockery as m;
use Packettide\Bree\Admin\FieldTypes;
use Packettide\Bree\Admin\Model as AdminModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class FieldInlineStackedTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		
	}

	/**
	 * Destroy the test environment.
	 */
	public function tearDown()
	{
		m::close();
	}


	// display
	public function testDisplay()
	{

		// $authorAdmin = new AdminModel($this->author, array(
		// 	'name'       => array('type' => 'TextArea', 'label' => "Name"),
		// 	'birth_date' => array('type' => 'Text'),
		// ));

		// $bookAdmin = new AdminModel($this->book, array(
		// 	'title' => array('type' => 'Text'),
		// 	'author' => array('type' => 'InlineStacked', 'related' => $authorAdmin, 'title' => 'name', 'label' => 'Author')
		// ));

		// $model = $bookAdmin->find(1);


		$adminModel = m::mock('AdminModel');
		$adminModel->shouldReceive('all')->once()->andReturn('1');

		$relation = m::mock('Relations\HasOne');

		$options = array(
			'title' => 'name',
			'label' => 'author',
			'related' => $adminModel,
			'relation' => $relation
		);

		$author = new FieldTypes\InlineStacked('author', '', $options);

		// No options passed
		$this->assertEquals('<select name="author"></select>', $author->field());


		//$model->author = 2;

		//echo $model;
	}


	// save

	// need to test HasOne, HasMany, BelongsToOne, BelongsToMany




}

//should really stub the AdminModel right? Eloquent has already been tested...

class EloquentModelStub extends EloquentModel {
	protected $table = 'stub';
	protected $guarded = array();
	public function getListItemsAttribute($value)
	{
		return json_decode($value, true);
	}
	public function setListItemsAttribute($value)
	{
		$this->attributes['list_items'] = json_encode($value);
	}
	public function getPasswordAttribute()
	{
		return '******';
	}
	public function setPasswordAttribute($value)
	{
		$this->attributes['password_hash'] = md5($value);
	}
	public function belongsToStub()
	{
		return $this->belongsTo('EloquentModelSaveStub');
	}
	public function morphToStub()
	{
		return $this->morphTo();
	}
	public function belongsToExplicitKeyStub()
	{
		return $this->belongsTo('EloquentModelSaveStub', 'foo');
	}
	public function getDates()
	{
		return array();
	}
}