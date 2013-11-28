# Extending Bree

Bree is designed to be easily extensible by allowing field type implementations to be self contained packages. There are two basic types of resources: field types and field sets.

## Field Types

Field types contain the markup and any additional assets necessary to generate a specialized form input.  A field type can have a unique implementation for different field sets.

## Field Sets

Field sets are a container for a group of field types.  Right now the main function of a field set is to provide common assets across its field types.

### Creating a Field Type

A field type, in its most basic form, is just a class that extends `Packettide\Bree\FieldType` and implements the `field()` function.

### Creating a Field Set


A field set must extend `Packettide\Bree\FieldSet` and define a `$name` for the class.

### Registering your Field Types and Field Sets

Each field package must have a service provider which extends `Illuminate\Support\ServiceProvider`.  The field type and/or field set will be attached in this service provider's `boot()` method like so:

	// Register a new Field Set
	Packettide\Bree\FieldSetProvider::register('Packettide\Bree\FieldSets\BasicFieldSet');

	// Specify the field set and then an array with a key/value of a name for the field type and the class in your package which fulfills the implementation
	Packettide\Bree\FieldSetProvider::attachFields('basic', array('Colorpicker' => 'Packettide\Colorpicker\Colorpicker'));

### An Example

Please refer to the [Colorpicker field package](https://github.com/packettide/bree-colorpicker) as an example of how to create and register your own field type.