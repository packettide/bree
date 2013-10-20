<?php namespace Packettide\Bree;

// Any benefit to extending Illuminate\Support\Collection ?
class AssetCollection {

	protected $assets = array();
	public $sorted = false;

	public function add($asset)
	{
		$this->assets[] = $asset;
		$this->sorted = false;
	}

	public function get($ext)
	{

	}

	public function all()
	{

	}

	public function sort()
	{
		if( ! $this->sorted)
		{
			$sorted = array();

			// extract extension and group assets
			foreach($this->assets as $asset)
			{
				$asset = $this->sortAsset($asset);

				$sorted = array_merge_recursive($sorted, $asset);
			}

			$this->sorted = true;
			$this->assets = $sorted;
		}
	}

	public function sortAsset($asset)
	{
		preg_match('/\.([^.]*)$/', $asset, $matches);
		$ext = $matches[1];

		return ($ext) ? array($ext => $asset) : $asset;
	}

	public function merge(AssetCollection $collection)
	{

	}


}