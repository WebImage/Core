<?php

namespace WebImage\Core;

use ArrayAccess;
use Countable;
use Iterator;

class Dictionary implements Countable, Iterator, ArrayAccess
{
	protected bool $immutable = false;

	/**
	 * @var array Data
	 */
	protected array $data = array();

	public function __construct(array $data=[])
	{
		$this->setData($data);
	}

	protected function setData(array $data)
	{
		$this->data = $data;
	}

	/**
	 * Merge the results from another dictionary into this one
	 *
	 * @param Dictionary $merge
	 */
	public function merge(Dictionary $merge)
	{
		$this->assertNotImmutable();

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
			}
		}
	}

	/**
	 * Set a key value
	 *
	 * @param string|array<string, mixed> $name
	 * @param mixed $value
	 */
	public function set($name, $value)
	{
		$this->assertNotImmutable();
		if (is_array($name)) {
			$value = new static($name);
		}

		if (null === $name) {
			$this->data[] = $value;
		} else {
			$this->data[$name] = $value;
		}
	}

	/**
	 * Return an associative array of the stored data.
	 *
	 * @return array
	 */
	public function toArray(): array
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
	public function keys(): array
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
	public function valid(): bool
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
	public function offsetExists($offset): bool
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
		$this->assertNotImmutable();
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
		$this->assertNotImmutable();
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
	public function count(): int
	{
		return count($this->data);
	}

	public function __unset($name)
	{
		$this->assertNotImmutable();
		if ($this->has($name)) {
			unset($this->data[$name]);
		}
	}

	public function __get($name)
	{
		return $this->get($name);
	}

	public function __set($name, $value)
	{
		$this->assertNotImmutable();
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

	public function has($name): bool
	{
		return (array_key_exists($name, $this->data));
	}

	public function del($name)
	{
		$this->assertNotImmutable();
		$this->__unset($name);
	}

	protected function assertNotImmutable(): void
	{
		if ($this->immutable) {
			throw new \RuntimeException('This object is immutable');
		}
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
