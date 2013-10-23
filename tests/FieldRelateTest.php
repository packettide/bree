<?php

use Mockery as m;
use Packettide\Bree\FieldTypes;
use Packettide\Bree\Model as BreeModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class FieldRelateTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$this->author1 = new EloquentModelStub(array('id' => 1, 'name' => 'C.S. Lewis'));
		$this->author2 = new EloquentModelStub(array('id' => 2, 'name' => 'JRR Tolkien'));

		$this->book1 = new EloquentModelStub(array('id' => 1, 'name' => 'The Horse and His Boy'));
		$this->book2 = new EloquentModelStub(array('id' => 2, 'name' => 'The Silver Chair'));
	}

	/**
	 * Destroy the test environment.
	 */
	public function tearDown()
	{
		m::close();
	}

	// @todo testBelongsToManyDisplay


	public function testHasManyDisplay()
	{
		$available = new Collection(array($this->book1, $this->book2));
		$chosen = $available;

		$adminModel = m::mock('Packettide\Bree\Model');
		$adminModel->shouldReceive('all')->once()->andReturn($available);

		$builder = m::mock('Illuminate\Database\Eloquent\Builder');
		$builder->shouldReceive('where')->with('books.author_id', '=', 1);
		$related = m::mock('Illuminate\Database\Eloquent\Model');
		$builder->shouldReceive('getModel')->andReturn($related);

		$parent = m::mock('Illuminate\Database\Eloquent\Model');
		$parent->shouldReceive('getKey')->andReturn(1);
		$parent->shouldReceive('getCreatedAtColumn')->andReturn('created_at');
		$parent->shouldReceive('getUpdatedAtColumn')->andReturn('updated_at');

		$relation = new Relations\HasMany($builder, $parent, 'books.author_id');


		$options = array(
			'title' => 'name',
			'label' => 'author',
			'related' => $adminModel,
			'relation' => $relation
		);

		$books = new FieldTypes\Relate('books', $chosen, $options);

		$this->assertEquals('<select multiple name="books[]" id="books"><option selected value="1">The Horse and His Boy</option><option selected value="2">The Silver Chair</option></select>', $books->field());
	}


	// display
	public function testBelongsToDisplay()
	{
		$available = new Collection(array($this->author1, $this->author2));

		$adminModel = m::mock('Packettide\Bree\Model');
		$adminModel->shouldReceive('all')->once()->andReturn($available);

		$relation = m::mock('Illuminate\Database\Eloquent\Relations\BelongsTo');

		$options = array(
			'title' => 'name',
			'label' => 'author',
			'related' => $adminModel,
			'relation' => $relation
		);

		$author = new FieldTypes\Relate('author', 1, $options);

		$this->assertEquals('<select name="author" id="author"><option selected value="1">C.S. Lewis</option><option value="2">JRR Tolkien</option></select>', $author->field());
	}


	public function testBelongsToDisplayNoOptions()
	{
		$adminModel = m::mock('Packettide\Bree\Model');
		$adminModel->shouldReceive('all')->once()->andReturn('');

		$relation = m::mock('Illuminate\Database\Eloquent\Relations\BelongsTo');

		$options = array(
			'title' => 'name',
			'label' => 'author',
			'related' => $adminModel,
			'relation' => $relation
		);

		$author = new FieldTypes\Relate('author', '', $options);

		// No options passed
		$this->assertEquals('<select name="author" id="author"></select>', $author->field());
	}

	/**
	 * Test Save Functionality Across Relationships
	 */

	// @todo testBelongsToManySave


	public function testBelongsToSave()
	{
		$available = new Collection(array($this->author1, $this->author2));

		$adminModel = m::mock('Packettide\Bree\Model');

		$relation = m::mock('Illuminate\Database\Eloquent\Relations\BelongsTo');
		$relation->shouldReceive('associate')->once();

		$options = array(
			'title' => 'name',
			'label' => 'author',
			'related' => $adminModel,
			'relation' => $relation
		);

		$author = new FieldTypes\Relate('author', $this->author1, $options);

		$author->save();

	}

	public function testHasOneSave()
	{
		$available = new Collection(array($this->author1, $this->author2));

		$adminModel = m::mock('Packettide\Bree\Model');

		$relation = m::mock('Illuminate\Database\Eloquent\Relations\HasOne');
		$relation->shouldReceive('save')->once();

		$options = array(
			'title' => 'name',
			'label' => 'author',
			'related' => $adminModel,
			'relation' => $relation
		);

		$author = new FieldTypes\Relate('author', $this->author2, $options);

		$author->save();
	}

	public function testHasManySave()
	{
		$available = new Collection(array($this->author1, $this->author2));
		$chosen = array($this->author1, $this->author2);

		$adminModel = m::mock('Packettide\Bree\Model');

		$relation = m::mock('Illuminate\Database\Eloquent\Relations\HasMany');
		$relation->shouldReceive('saveMany')->once();

		$options = array(
			'title' => 'name',
			'label' => 'author',
			'related' => $adminModel,
			'relation' => $relation
		);

		$author = new FieldTypes\Relate('author', $chosen, $options);

		$author->save();
	}

	// save

	// need to test HasOne, HasMany, BelongsToOne, BelongsToMany




}

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