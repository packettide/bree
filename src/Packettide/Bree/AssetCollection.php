<?php namespace Packettide\Bree;

class AssetCollection {

	protected $assetGroups = array();

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
			$this->assetGroups[key($asset)] = array('assets' => array(), 'published' => false);
		}

		$this->assetGroups = array_merge_recursive($this->assetGroups, array(key($asset) => array('assets' => array_values($asset))));
	}

	public function publishAll()
	{
		$all = '';

		foreach(array_keys($this->assetGroups) as $key)
		{
			$all .= $this->publish($key);
		}

		return $all;
	}

	public function publish($ext)
	{
		if(array_key_exists($ext, $this->assetGroups))
		{
			$assetGroup = $this->assetGroups[$ext];
			$assets = '';

			if( ! $assetGroup['published'])
			{
				echo 'not published';
				$this->assetGroups[$ext]['published'] = true;
				var_dump($this->assetGroups);
				$assets = $this->toHTML($ext, $this->get($ext));
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
		$all = array();

		foreach(array_keys($this->assetGroups) as $key)
		{
			$all = array_merge($all, $this->get($key));
		}

		return $all;
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


	// Do we need to keep this around?
	public function sort($assets)
	{
		$sorted = array();

		// extract extension and group assets
		foreach($assets as $asset)
		{
			$asset = $this->sortAsset($asset);

			$sorted = array_merge_recursive($sorted, $asset);
		}

		return $sorted;
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

		return ($ext) ? array($ext => $asset) : $asset;
	}

	public function toHTML($type, $assets)
	{
		$html = '';

		foreach($assets as $asset)
		{
			$html .= $this->generateAssetLink($type, $asset)."\n";
		}

		return $html;
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

	public function __get($key)
	{
		return $this->publish(strtolower($key));
	}

	public function __toString()
	{
		return $this->publishAll();
	}


	public function merge($collection)
	{
		if(is_array($collection))
		{
			$collection = new static($collection);
		}

		if($collection instanceof AssetCollection)
		{
			// both collections should be sorted
			$result = array_merge_recursive($this->all(), $collection->all());
			return new static($result);
		}
	}


}