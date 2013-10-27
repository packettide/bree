<?php namespace Packettide\Bree;

abstract class FieldSet {

	protected $fieldtypes = array();
	protected $assets = array();
	protected $assetsPublished = array();
	public $name;


	/**
	 * Retrieve the FieldSet's name
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Get a list of all associated FieldTypes
	 * @return [type] [description]
	 */
	public function allFieldTypes()
	{
		return $this->fieldtypes;
	}

	/**
	 * Retrieve the requested FieldType if present
	 * @param  string $name
	 * @return string|Packettide/Bree/FieldType
	 */
	public function retrieveFieldType($name)
	{
		$fieldtype = '';

		if(array_key_exists($name, $this->fieldtypes))
		{
			$fieldtype = $this->fieldtypes[$name];
		}

		return $fieldtype;
	}

	/**
	 * Add one or more FieldTypes to the FieldSet
	 * @param  array  $fieldtypes [description]
	 * @return
	 */
	public function attach($fieldtypes = array())
	{
		$this->fieldtypes = array_merge($this->fieldtypes, $fieldtypes);
	}

	/**
	 * Sort the FieldSets assets into buckets based on extension
	 * @param  array $assets
	 * @return array
	 */
	public function sortAssets($assets)
	{
		$sorted = array();

		// extract extension and group assets
		foreach($assets as $asset)
		{
			preg_match('/\.([^.]*)$/', $asset, $matches);
			$ext = $matches[1];

			if($ext)
			{
				$sorted[$ext][] = $asset;
			}
		}

		return $sorted;
	}

	/**
	 * Return FieldSet's assets
	 * @return array
	 */
	public function getAssets()
	{
		return $this->assets;
	}

	/**
	 * Retrieve the assets for a fieldset
	 * @return array
	 */
	public function assets()
	{
		$assets = array();

		// Make sure the assets are only published once
		if(!isset($this->assetsPublished[$this->getName()]))
		{
			$this->assetsPublished[$this->getName()] = true;
			$assets = $this->publishAssets();
		}

		return $assets;
	}

	/**
	 * Merge assets for the FieldSet with all dependent FieldTypes
	 * @return array
	 */
	public function publishAssets()
	{
		$allAssets = array_merge_recursive($this->fieldSetAssets(), $this->fieldTypeAssets());
		return $allAssets;
	}

	/**
	 * Generate an array of assets for the FieldSet
	 * @return array
	 */
	public function fieldSetAssets()
	{
		$paths = array();
		$assetCollection = $this->sortAssets($this->assets);

		foreach($assetCollection as $assetType => $assets)
		{
			foreach($assets as $asset)
			{
				$paths[$assetType][] = $this->generateAssetLink($assetType, $asset);
			}
		}

		return $paths;
	}

	/**
	 * Generate an array of assets for all dependent FieldTypes
	 * @return array
	 */
	public function fieldTypeAssets()
	{
		$paths = array();

		foreach($this->fieldtypes as $fieldtype)
		{
			// @todo Abstract this out to a helper method, same as above for fieldsets
			$assetCollection = $this->sortAssets($fieldtype::assets());

			foreach($assetCollection as $assetType => $assets)
			{
				foreach($assets as $asset)
				{
					$paths[$assetType][] = $this->generateAssetLink($assetType, $asset );
				}
			}
		}

		return $paths;
	}

	/**
	 * Helper to generate relevant HTML includes for assets
	 * @param  string $type     type of asset
	 * @param  string $filename asset's filename
	 * @return string           HTML include
	 */
	public function generateAssetLink($type, $filename)
	{
		$link = '';

		$filename = asset('packages/' . $filename);

		switch ($type) {
			case 'css':
				$link = '<link rel="stylesheet" href="'.$filename.'">';
				break;
			case 'js':
				$link = '<script src="'.$filename.'"></script>';
				break;
		}

		return $link;
	}

}