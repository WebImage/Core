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
	public function get($name, $default = null, $dotTraverse=true)
	{
		$keys = $dotTraverse ? explode('.', $name) : [$name];

		$value = parent::get($keys[0], $default);

		if ($value != $default) {
			for ($i = 1, $j = count($keys); $i < $j; $i++) {
				if ($value instanceof self) $value = $value->get($keys[$i], $default);
			}
		}

		return $value;
	}
}
