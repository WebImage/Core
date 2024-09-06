<?php

namespace WebImage\Config;

use RuntimeException;
use WebImage\Core\ArrayHelper;
use WebImage\Core\Dictionary;

class Config extends Dictionary
{
	protected function setData(array $data)
	{
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				$data[$key] = $this->processArray($value);
			}
		}

		parent::setData($data);
	}

	/**
	 * Traverse an array and process associative arrays as instances of the static class type
	 * @param array $values
	 *
	 * @return array|static
	 */
	private function processArray(array $values)
	{
		if (ArrayHelper::isAssociative($values)) return new static($values);

		foreach ($values as $key => $val) {
			if (is_array($val)) $values[$key] = $this->processArray($val);
		}

		return $values;
	}

	/**
	 * Adds additional functionality to parent get(...) method by allowing dot syntax to delve into a hierarchy
	 *
	 * @param string $name
	 * @param mixed|null $default
	 * @param bool $dotTraverse Whether a dot (".") in the name should be used to traverse through the config hierarchy (false treats name as flat name)
	 *
	 * @return mixed|null
	 */
	public function get($name, $default = null, bool $dotTraverse = true)
	{
		$keys = $this->normalizeKeyPath($name, $dotTraverse);
		$key  = array_shift($keys);

		if (array_key_exists($key, $this->data)) {
			if (count($keys) == 0) {
				return $this->data[$key];
			} else if ($this->data[$key] instanceof self) {
				return $this->data[$key]->get(implode('.', $keys), $default, $dotTraverse);
			}
		}

		return $default;
	}

	public function has($name, bool $dotTraverse = true): bool
	{
		$keys = $this->normalizeKeyPath($name, $dotTraverse);
		$key  = array_shift($keys);

		// Traverse through remaining keys
		if (array_key_exists($key, $this->data)) {
			if ($this->data[$key] instanceof self) {
				return $this->data[$key]->has(implode('.', $keys), $dotTraverse);
			}

			return count($keys) == 0;
		}

		return false;
	}

	/**
	 * Take a string key and create an array, where each element is a key to be traversed.
	 * If the first key starts with [ then anything inside the brackets is the first key, and anything after is the second key.
	 * Only deal with a maximum of two keys at a time, since subsequent request would be for the remaining keys, which can be handled by recursion
	 *
	 * @param string|mixed $key
	 * @TODO Can we convert $key to string.  There may be unknown cases where the key can be any TYPE.
	 * @param bool $dotTraverse
	 * @return array The key path, with each key as an element (will only work with a maximum of 2 )
	 */
	private function normalizeKeyPath($key, bool $dotTraverse = true): array
	{
		if (!$dotTraverse) return [$key];

		if (substr($key, 0, 1) == '[') {
			$finalPos = strpos($key, ']', 1);
			if ($finalPos === false) throw new RuntimeException('Used "[" without matching "]" in ' . $key);
			$compoundKey = substr($key, 1, $finalPos - 1);
			$remaining = substr($key, $finalPos + 2);

			$parts = [$compoundKey];
			if (!empty($remaining)) $parts[] = $remaining;
			return $parts;
		}

		return explode('.', $key, 2);
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
