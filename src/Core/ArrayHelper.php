<?php

namespace WebImage\Core;

use InvalidArgumentException;

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
	 *
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

	/**
	 * Check if an array is an associative array.
	 * Empty arrays will return TRUE, so it is imperative to check for empty arrays first.
	 *
	 * @param array $arr
	 *
	 * @return bool
	 */
	public static function isAssociative(array $arr): bool
	{
		return array_keys($arr) !== range(0, count($arr)-1);
	}

	/**
	 * Ensure that an array only has the specified required or optional keys
	 *
	 * @param array $arr The array to check
	 * @param string $pathHint A description to help indicate where in an array hierarchy the array/key is located
	 * @param array $requiredProperties The keys that MUST be present in the array to be successful
	 * @param array $optionalProperties The keys that CAN be present (anything not in $required or $optional are considered invalid)
	 *
	 * @throws InvalidArgumentException
	 */
	public static function assertKeys(array $arr, $pathHint, array $requiredProperties, array $optionalProperties=[])
	{
		$allowedFields = array_merge($requiredProperties, $optionalProperties);

		foreach($requiredProperties as $requiredProperty) {
			if (!array_key_exists($requiredProperty, $arr)) throw new InvalidArgumentException($pathHint . ' missing required property: ' . $requiredProperty);
		}

		foreach(array_keys($arr) as $property) {
			if (!in_array($property, $allowedFields)) throw new InvalidArgumentException($pathHint . ' has unknown property: ' . $property);
		}
	}

	public static function assertItemTypes(array $arr, string $type)
	{
		foreach($arr as $item) {
			$itemType = gettype($item);
			if ($itemType != 'object' && $itemType != $type) throw new InvalidArgumentException('Expecting ' . $type . ' but found ' . $itemType);
			else if ($itemType == 'object' && !($item instanceof $type)) throw new InvalidArgumentException('Expecting ' . $type . ' but found ' . (is_object($item) ? get_class($item) : 'x' . $itemType));
		}
	}
}
