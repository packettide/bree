# Field: Relate

Easily bind related models to each other.
This field currently supports the following relation types: HasOne, BelongsTo, and HasMany

Required Parameters:

	'type' => 'Relate',
	'related' => Bree\Model|string,
	'title' => string

Optional Parameters:

	'select' => true|false
