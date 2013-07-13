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

	public function testHasManyDisplay()
	{
		$book1 = new EloquentModelStub(array('id' => 1, 'name' => 'The Horse and His Boy'));
		$book2 = new EloquentModelStub(array('id' => 2, 'name' => 'The Silver Chair'));

		$available = new Collection(array($book1, $book2));
		$chosen = $available;

		$adminModel = m::mock('AdminModel');
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

		$books = new FieldTypes\InlineStacked('books', $chosen, $options);

		$this->assertEquals('<select multiple name="books[]"><option selected value="1">The Horse and His Boy</option><option selected value="2">The Silver Chair</option></select>', $books->field());

	}


	// display
	public function testHasOneDisplay()
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

		$author1 = new EloquentModelStub(array('id' => 1, 'name' => 'C.S. Lewis'));
		$author2 = new EloquentModelStub(array('id' => 2, 'name' => 'JRR Tolkien'));

		$available = new Collection(array($author1, $author2));

		$adminModel = m::mock('AdminModel');
		$adminModel->shouldReceive('all')->once()->andReturn($available);

		$relation = m::mock('Relations\HasOne');

		$options = array(
			'title' => 'name',
			'label' => 'author',
			'related' => $adminModel,
			'relation' => $relation
		);

		$author = new FieldTypes\InlineStacked('author', $author1, $options);

		// No options passed
		$this->assertEquals('<select name="author"><option selected value="1">C.S. Lewis</option><option value="2">JRR Tolkien</option></select>', $author->field());


		//$model->author = 2;

		//echo $model;
	}

	public function testHasOneDisplayNoOptions()
	{
		$adminModel = m::mock('AdminModel');
		$adminModel->shouldReceive('all')->once()->andReturn('');

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