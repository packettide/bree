<?php namespace Packettide\Bree\Assets;

class Bundle {

	/**
	 * A 2d array of assets organized by extension
	 * @var array
	 */
	protected $assetGroups = array();

	/**
	 * Load an array of assets into the bundle
	 * @param array $assets
	 */
	public function __construct($assets=array())
	{
		foreach($assets as $asset)
		{
			$this->add($asset);
		}
	}

	/**
	 * Add an asset to the collection after "sorting" it
	 * @param string $asset
	 */
	public function add($asset)
	{
		$asset = $this->sortAsset($asset);

		if(!array_key_exists(key($asset), $this->assetGroups))
		{
			$this->assetGroups[key($asset)] = array();
		}

		$this->assetGroups[key($asset)] = array_merge($this->assetGroups[key($asset)], array_values($asset));
	}

	/**
	 * Publish all asset groups in the bundle
	 * @return string
	 */
	public function publishAll()
	{
		$all = '';

		foreach($this->assetGroups as $key => $value)
		{
			$all .= $this->publish($key);
		}

		return $all;
	}

	/**
	 * Publish assets with a chosen extension in the bundle
	 * @param  string $ext
	 * @return string
	 */
	public function publish($ext)
	{
		if(array_key_exists($ext, $this->assetGroups) && !empty($this->assetGroups[$ext]))
		{
			$assets = '';

			foreach($this->assetGroups[$ext] as &$asset)
			{
				// Assets should only be "published" once per page request
				if(!isset($asset['published']))
				{
					$asset['published'] = true;
					$assets .= $this->generateAssetLink($ext, $asset['path'])."\n";
				}
			}

			return $assets;
		}
	}

	/**
	 * Get all of the assets
	 * @return array
	 */
	public function all()
	{
		return $this->assetGroups;
	}

	/**
	 * Get all assets with a specified extension
	 * @param  string $ext
	 * @return array
	 */
	public function get($ext)
	{
		return (array_key_exists($ext, $this->assetGroups)) ? $this->assetGroups[$ext]['assets'] : array();
	}

	/**
	 * Indicate whether the Bundle is empty or not
	 * @return boolean
	 */
	public function isEmpty()
	{
		return empty($this->assetGroups);
	}

	/**
	 * Sort an asset into an array with the file extension as a key
	 * @param  string $asset
	 * @return array
	 */
	public function sortAsset($asset)
	{
		preg_match('/\.([^.]*)$/', $asset, $matches);
		$ext = strtolower($matches[1]);

		// nest the path so we can later add a flag for published
		$asset = array('path' => $asset);
		return ($ext) ? array($ext => $asset) : $asset;
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