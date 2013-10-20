<?php namespace Packettide\Bree;

abstract class FieldSet {

	protected $fieldtypes = array();
	protected $assets = array();
	protected $assetsPublished = array();
	public $name;


	public function attach($fieldtypes = array())
	{
		$this->fieldtypes = array_merge($this->fieldtypes, $fieldtypes);
	}

	public function retrieveFieldType($name)
	{
		$fieldtype = '';

		if(array_key_exists($name, $this->fieldtypes))
		{
			$fieldtype = $this->fieldtypes[$name];
		}

		return $fieldtype;
	}

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

	public function assets()
	{
		$assets = array();

		if(!isset($this->assetsPublished[$this->getName()]))
		{
			$this->assetsPublished[$this->getName()] = true;
			$assets = $this->publishAssets();
		}

		return $assets;
	}

	public function publishAssets()
	{
		$allAssets = array_merge_recursive($this->fieldSetAssets(), $this->fieldTypeAssets());
		return $allAssets;
	}

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

	public function getName() {
		return $this->name;
	}


}