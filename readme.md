
![bree](https://f.cloud.github.com/assets/563996/1727115/df8b5ae0-6299-11e3-8728-a61970b58113.png)

#Bree

Bree provides an interface to associate fieldtypes with existing Eloquent model attributes.

*This project is still in an early stage and breaking changes may be made.*

[![Build Status](https://travis-ci.org/packettide/bree.png?branch=master)](https://travis-ci.org/packettide/bree)


##Installation

With Laravel 4 and Composer

1. Add `"packettide/bree": "@dev"` to your 'require' block in composer.json
2. Run `composer update`
3. Add `'Packettide\Bree\BreeServiceProvider',` to the $providers array in app/config/app.php
4. Then publish all Bree assets - `php artisan bree:assets`


##Basic Usage

In this section we'll cover the fundamentals of how to interact with Bree.
Bree functions by wrapping an existing Eloquent model and attaching field definitions to model attributes.

### Defining Bree fields within routes/controllers

    $book = new Bree('Book', array(
				'title'  => array('type' => 'Text')
			));


### Defining Bree fields within Eloquent Models

In app/models/Book.php

	<?php

	class Book extends Eloquent {

		public $breeFields = array(
			'title' => array('type' => 'Text')
		);

Note that you can define a base field mapping in your model and override fields in a route if desired.

### Display a new model

	$book = new Bree('Book');
	echo $book; //this will output fields for all defined attributes

### Display an existing model

	$book = new Bree('Book');
	$book->find(1);
	echo $book;

### Displaying a Bree field

If you don't wish to display all Bree fields from a model or would like to control the order you can use the model attributes in your views like so:

	// Route
	$book = new Bree('Book');
	return View::make('books.create', array('book' => $book));

	// View
	{{ $book->title }} // this will output the label if available, followed by the field
	{{ $book->title->field() }} // outputs the field only
	{{ $book->title->label() }} // outputs the label only


### Updating/Saving data

	$input = Input::except('_token');
	$book = new Bree('Book');

	foreach($input as $key => $value)
	{
		$book->$key = $value;
	}

	$book->save();



## Field Definitions

Here is an example of what a field definition looks like:

	array('comments' => array('type' => 'Relate', 'label' => 'Book Comments', 'related' => 'Comment', 'title' => 'title'))

* The key of this array (comments) is the name of the Eloquent attribute or relation that this field should be associated with.
* **Type**: This is the name of the field type
* **Label**: This is an optional attribute used to generate a corresponding HTML label for the field


### Core Field Types

The following field types are available by default:

* [Cell](docs/field-cell.md) - Allows inline modification of a related Bree model
* [Date](docs/field-date.md) - HTML5 date input
* [File](docs/field-file.md) - A single file upload field
* [Relate](docs/field-relate.md) - Easily bind related models to each other
* [Text](docs/field-text.md) - Simple text input
* [Textarea](docs/field-textarea.md) - Simple textarea input
* [Time](docs/field-time.md) - HTML5 time input


## Using Other Fields

Adding another field package to your project typically involves adding the dependency to your composer.json file and registering the service provider with your application.  Here are a few field packages which have more detailed instructions on their project pages:

* [Colorpicker](https://github.com/packettide/bree-colorpicker)
* [Wysiwyg](https://github.com/packettide/bree-wysiwyg)

## Scaffolding with Bree

While Bree is a simple layer to add over an existing Eloquent model you may find it tedious to setup the mapping of fields to attributes and model relations etc…  We have another [package called Sire](https://github.com/packettide/sire), which helps alleviate this problem while also providing a simple way to scaffold an application with Bootstrap 3.

## Extending Bree

Bree is built to be a flexible and extensible base allowing for the creation of field types as they are required by a project.  [To learn more about creating your own field package start here](docs/extend.md).


### License

Bree is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)