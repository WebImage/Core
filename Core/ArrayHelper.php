<?php

namespace WebImage\Core;

class ArrayHelper {

	/**
	 * Merge an array recursively without clobbering previously set key structures
	 *
	 * @return array
	 */
	public static function merge(array $_)
	{
		$tgt = [];

		foreach(func_get_args() as $merge) {
			foreach($merge as $key => $val) {
				if (array_key_exists($key, $tgt)) {
					if (is_int($key)) {
						$tgt[] = $val;
					} else {
						if (is_array($val)) {
							$tgt[$key] = static::merge($tgt[$key], $val);
						} else {
							$tgt[$key] = $val;
						}
					}
				} else {
					$tgt[$key] = $val;
				}
			}
		}

		return $tgt;
	}

	/**
	 * Finds the first element in an array that matches the callable
	 *
	 * @param array $array
	 * @param callable $callable $callable($val, $key) Tests value for trueness and returns TRUE if criteria matches
	 * @return mixed|void Mixed if value found, void if not
	 */
	public static function first(array $array, callable $callable)
	{
		foreach($array as $key => $val) {
			$test = call_user_func($callable, $val, $key);

			if (true === $test) {
				return $val;
			}
		}
	}
}