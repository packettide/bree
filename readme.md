#Bree

Bree provides an interface to associate fieldtypes with existing Eloquent model attributes.

##Installation

Install with composer by adding this line to your 'require' block:

    "packettide/bree": "@dev"

In Laravel4 add `'Packettide\Bree\BreeServiceProvider',` to the providers array in app/config/app.php

##Usage Overview

    $book = new Bree('Book', array(
			'title'  => array('type' => 'Text'),
			'author' => array('type' => 'InlineStacked', 'related' => 'Author', 'title' => 'name'),
			'cover'  => array('type' => 'File', 'directory' => ''.public_path().'/covers/', 'fieldset' => 'advanced'),
			'comments' => array('type' => 'InlineStacked', 'related' => 'Comment', 'title' => 'title')
		));

	$book->find(1);
	echo $book; //this will output fields for all defined attributes

Alternatively you can define fieldtype mappings within the Eloquent model

In app/models/Book.php

	<?php

	class Book extends Eloquent {

		public $breeFields = array(
			'title' => array('type' => 'Text')
		);

And then the route/controller would be simplified

	$book = new Bree('Book');

	$book->find(1);
	echo $book; //this will output fields for all defined attributes

Note that you can define a base field mapping in your model and override it in a route if needed.

## Field Definitions

Here is an example of what a field definition looks like

	array('comments' => array('type' => 'InlineStacked', 'label' => 'Book Comments', 'related' => 'Comment', 'title' => 'title'))

* The key of this array (comments) is the name of the Eloquent attribute or relation that this field should be associated with.
* **Type**: This is the name of the field type


###Core FieldTypes

* InlineStacked with relations - HasOne, BelongsTo, HasMany
* File Upload (Single File Only)
* TextArea
* Text

### License

Bree is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)