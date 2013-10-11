<?php namespace Packettide\Bree;

abstract class FieldSet {

	protected $fieldtypes = array();
	protected static $assets = array();
	protected static $assetsPublished = array();


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

	public static function sortAssets()
	{
		$assets = static::$assets;
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

		static::$assets = $sorted;
	}

	public static function assets()
	{
		if(!isset(static::$assetsPublished[static::getName()]))
		{
			static::$assetsPublished[static::getName()] = true;
			return static::publishAssets();
		}
	}

	public static function publishAssets()
	{
		$includes = '';

		foreach(static::$assets as $assetType => $assets)
		{
			foreach($assets as $asset)
			{

				$includes .= static::generateAssetLink($assetType, $asset) ."\n";
			}
		}
		return $includes;
	}

	public static function generateAssetLink($type, $filename)
	{
		$link = '';
		$filename = asset('packages/packettide/bree/'.$filename);

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

	abstract public static function getName();


}