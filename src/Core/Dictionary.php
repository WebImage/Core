<?php

namespace WebImage\Core;

use ArrayAccess;
use Countable;
use Iterator;

class Dictionary implements Countable, Iterator, ArrayAccess
{
	/**
	 * @var array Data
	 */
	protected $data = array();
	protected $count = 0;

	public function __construct(array $data=[])
	{
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				$this->data[$key] = $this->processArray($value);
			} else {
				$this->data[$key] = $value;
			}

			$this->count++;
		}
	}

	/**
	 * Traverse an array and process associative arrays as instances of the static class type
	 * @param array $values
	 *
	 * @return array|static
	 */
	private function processArray(array $values)
	{
		if ($this->isAssocArray($values)) return new static($values);

		foreach($values as $key => $val) {
			if (is_array($val)) $values[$key] = $this->processArray($val);
		}

		return $values;
	}

	private function isAssocArray(array $data)
	{
		return count($data) > 0 && array_keys($data) !== range(0, count($data) - 1);
	}

	/**
	 * Merge the results from another dictionary into this one
	 *
	 * @param Dictionary $merge
	 */
	public function merge(Dictionary $merge)
	{
		foreach ($merge as $key => $value) {
			if (array_key_exists($key, $this->data)) {
				if (is_int($key)) {
					$this->data[] = $value;
				} elseif ($value instanceof static && $this->data[$key] instanceof static) {
					$this->data[$key]->merge($value);
				} else if (is_array($value) && is_array($this->data[$key])) {
					$this->data[$key] = ArrayHelper::merge($this->data[$key], $value);
				} else {
					if ($value instanceof static) {
						$this->data[$key] = new static($value->toArray());
					} else {
						$this->data[$key] = $value;
					}
				}
			} else {
				if ($value instanceof static) {
					$this->data[$key] = new static($value->toArray());
				} else {
					$this->data[$key] = $value;
				}
				$this->count++;
			}
		}
	}

	/**
	 * Set a key value
	 *
	 * @param string|array[string] $name
	 * @param mixed $value
	 */
	public function set($name, $value)
	{
		if (is_array($name)) {
			$value = new static($name);
		}

		if (null === $name) {
			$this->data[] = $value;
		} else {
			$this->data[$name] = $value;
		}

		$this->count++;
	}

	/**
	 * Return an associative array of the stored data.
	 *
	 * @return array
	 */
	public function toArray()
	{
		$array = array();
		$data = $this->data;

		/** @var static $value */
		foreach ($data as $key => $value) {
			if ($value instanceof static) {
				$array[$key] = $value->toArray();
			} else {
				$array[$key] = $value;
			}
		}

		return $array;
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the current element
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 */
	public function current()
	{
		return current($this->data);
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Move forward to next element
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 */
	public function next()
	{
		next($this->data);
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the key of the current element
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 */
	public function key()
	{
		return key($this->data);
	}

	/**
	 * Get the defined keys
	 *
	 * @return array
	 */
	public function keys()
	{
		return array_keys($this->data);
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Checks if current position is valid
	 * @link http://php.net/manual/en/iterator.valid.php
	 * @return boolean The return value will be casted to boolean and then evaluated.
	 * Returns true on success or false on failure.
	 */
	public function valid()
	{
		return ($this->key() !== null);
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Rewind the Iterator to the first element
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 */
	public function rewind()
	{
		reset($this->data);
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Whether a offset exists
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 * @param mixed $offset <p>
	 * An offset to check for.
	 * </p>
	 * @return boolean true on success or false on failure.
	 * </p>
	 * <p>
	 * The return value will be casted to boolean if non-boolean was returned.
	 */
	public function offsetExists($offset)
	{
		return $this->__isset($offset);
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to retrieve
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 * @param mixed $offset <p>
	 * The offset to retrieve.
	 * </p>
	 * @return mixed Can return all value types.
	 */
	public function offsetGet($offset)
	{
		return $this->__get($offset);
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to set
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 * @param mixed $offset <p>
	 * The offset to assign the value to.
	 * </p>
	 * @param mixed $value <p>
	 * The value to set.
	 * </p>
	 * @return void
	 */
	public function offsetSet($offset, $value)
	{
		$this->__set($offset, $value);
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to unset
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 * @param mixed $offset <p>
	 * The offset to unset.
	 * </p>
	 * @return void
	 */
	public function offsetUnset($offset)
	{
		$this->__unset($offset);
	}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Count elements of an object
	 * @link http://php.net/manual/en/countable.count.php
	 * @return int The custom count as an integer.
	 * </p>
	 * <p>
	 * The return value is cast to an integer.
	 */
	public function count()
	{
		return $this->count;
	}

	public function __unset($name)
	{
		if ($this->has($name)) {
			unset($this->data[$name]);
			$this->count--;
		}
	}

	public function __get($name)
	{
		return $this->get($name);
	}

	public function __set($name, $value)
	{
		$this->set($name, $value);
	}

	public function __isset($name)
	{
		return $this->has($name);
	}

	public function get($name, $default = null)
	{
		return $this->has($name) ? $this->data[$name] : $default;
	}

	public function has($name)
	{
		return (array_key_exists($name, $this->data));
	}

	public function del($name)
	{
		return $this->__unset($name);
	}

	/**
	 * Deep clone of this instance to ensure that nested WebImage\Core\Dictionary's are also cloned.
	 *
	 * @return void
	 */
	public function __clone()
	{
		$array = array();

		foreach ($this->data as $key => $value) {
			if ($value instanceof static) {
				$array[$key] = clone $value;
			} else {
				$array[$key] = $value;
			}
		}

		$this->data = $array;
	}
}