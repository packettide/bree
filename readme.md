#Bree

Bree provides an interface to associate fieldtypes with existing Eloquent model attributes.

*This project is still in a very early stage and breaking changes may  be made.*

##Installation

1. Install with composer by adding this line to your 'require' block in composer.json:

    "packettide/bree": "@dev"
2. Run `composer update`

3. In Laravel4 add `'Packettide\Bree\BreeServiceProvider',` to the $providers array in app/config/app.php
4. Then publish the package's assets - `php artisan bree:assets`

(Optional) You may also want to add the bree:assets command to your post-update and post-install hooks in composer. This would look something like

	"scripts": {
		"pre-update-cmd": [
			"php artisan clear-compiled"
		],
		"post-install-cmd": [
			"php artisan optimize",
			"php artisan bree:assets"
		],
		"post-update-cmd": [
			"php artisan optimize",
			"php artisan bree:assets"
		]
	},


##Usage Overview

Bree functions by wrapping an existing Eloquent model and attaching field definitions to model attributes.

    $book = new Bree('Book', array(
			'title'  => array('type' => 'Text'),
			'author' => array('type' => 'Relate', 'related' => 'Author', 'title' => 'name'),
			'cover'  => array('type' => 'File', 'directory' => ''.public_path().'/covers/', 'fieldset' => 'advanced'),
			'comments' => array('type' => 'Relate', 'related' => 'Comment', 'title' => 'title')
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

	array('comments' => array('type' => 'Relate', 'label' => 'Book Comments', 'related' => 'Comment', 'title' => 'title'))

* The key of this array (comments) is the name of the Eloquent attribute or relation that this field should be associated with.
* **Type**: This is the name of the field type


### Core Field Types

The following field types are loaded by default:

* Date
* File Upload (Single File Only)
* Relate (Currently supports these relation types - HasOne, BelongsTo, HasMany)
* Text
* TextArea
* Time

### Create your own Field Package

[Read more about extending Bree](extend.md).


### License

Bree is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)