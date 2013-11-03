<?php namespace Packettide\Bree\Assets;

class Collection extends \Illuminate\Support\Collection {

	/**
	 * The collection assets with an extension that matches
	 * $key will be published
	 * @param  string $key
	 * @return string
	 */
	public function __get($key)
	{
		$html = '';

		foreach($this->items as $item)
		{
			$html .= $item->publish(strtolower($key));
		}

		return $html;
	}

	/**
	 * All of the collections assets will be published
	 * @return string
	 */
	public function __toString()
	{
		$html = '';

		foreach($this->items as $item)
		{
			$html .= $item->publishAll();
		}

		return $html;
	}

}