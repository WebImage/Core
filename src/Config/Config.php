<?php

namespace WebImage\Config;

use WebImage\Core\Dictionary;

class Config extends Dictionary {
	/**
	 * Adds additional functionality to parent get(...) method by allowing dot syntax to delve into a hierarchy
	 *
	 * @param string $name
	 * @param mixed|null $default
	 * @param bool $dotTraverse Whether a dot (".") in the name should be used to traverse through the config hierarchy (false treats name as flat name)
	 *
	 * @return mixed|null
	 */
	public function get($name, $default = null, bool $dotTraverse=true)
	{
		$keys = $dotTraverse ? explode('.', $name) : [$name];

		$value = $this->normalizeParentGet($keys[0], $default);

		if ($value != $default) {
			for ($i = 1, $j = count($keys); $i < $j; $i++) {
				if ($value instanceof self) $value = $value->get($keys[$i], $default);
			}
		}

		return $value;
	}

	/**
	 * Return $default When parent returns empty array
	 * Normally Dictionary::get(...) returns an empty array if a key is defined as an empty array, but Config should treat this the same as $default
	 * @param $name
	 * @param null $default
	 *
	 * @return mixed|null
	 */
	private function normalizeParentGet($name, $default = null)
	{
		$get = parent::get($name, $default);

		if (is_array($get) && count($get) == 0) {
			$get = $default;
		}

		return $get;
	}
}
