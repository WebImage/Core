<?php

namespace WebImage\Config;

use WebImage\Core\Dictionary;

class Config extends Dictionary {
	/**
	 * Adds additional functionality to parent get(...) method by allowing dot syntax to delve into a hierarchy
	 *
	 * @param string $name
	 * @param mixed|null $default
	 *
	 * @return mixed|null
	 */
	public function get($name, $default = null)
	{
		$keys = explode('.', $name);

		$value = parent::get($keys[0], $default);

		for($i=1, $j=count($keys); $i < $j; $i++) {
			if ($value !== $default) $value = $value->get($keys[$i], $default);
		}

		return $value;
	}

//	public function set($name, $value)
//	{
//		if (is_array($value)) {
//			$value = new static($value, true);
//		}
//
//		parent::set($name, $value);
//	}
}