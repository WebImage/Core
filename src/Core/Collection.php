<?php

namespace WebImage\Core;

use ArrayAccess;
use Countable;
use Iterator;

/**
 * @template T
 * @implements ArrayAccess<int, T>
 */
class Collection implements Countable, Iterator, ArrayAccess
{
	/**
	 * @var array Data
	 */
	protected array $data = array();

	/**
	 * Constructor
	 *
	 * @param T[] $data
	 */
	public function __construct(array $data=[])
	{
		foreach($data as $key => $value) {
			$this->offsetSet($key, $value);
		}
	}

	/**
	 * @param int $index
	 * @param $default
	 * @return T
	 */
	public function get(int $index, $default = null)/*: mixed @TODO PHP 8*/
	{
		return $this->has($index) ? $this->data[$index] : $default;
	}

	public function has(int $index): bool
	{
		return (array_key_exists($index, $this->data));
	}

	public function del(int $index): void
	{
		$this->__unset($index);
	}

	/**
	 * Add an item to the collection
	 *
	 * @param T $item
	 */
	public function add($item): void
	{
		$this->assertValidItem($item);
		$this->data[] = $item;
	}

	/**
	 * @param $index
	 * @param T $item
	 * @return void
	 */
	public function insert($index, $item): void
	{
		$this->assertValidItem($item);
		array_splice($this->data, $index, 0, [$item]);
	}

	/**
	 * Allows deriving classes that are meant to store a specific type of data to enforce that data type.
	 */
	protected function assertValidItem($item): void {}

	/**
	 * Return an associative array of the stored data.
	 *
	 * @return T[]
	 */
	public function toArray(): array
	{
		return $this->data;
	}

	/**
	 * Returns a clone of the original collection with items that have been transformed by the mapper
	 * @param callable $mapper
	 * @return $this
	 */
	public function map(callable $mapper): Collection
	{
		$c = clone $this;
		foreach($this as $key => $val) {
			$c[$key] = call_user_func($mapper, $val);
		}

		return $c;
	}

	/**
	 * Returns a clone of the original collection with items that do not pass the filter removed
	 * @param callable $filterCallback
	 * @return $this
	 */
	public function filter(callable $filterCallback): Collection
	{
		$c = clone $this;
		$c->data = [];

		foreach($this as $key => $item) {
			$include = call_user_func($filterCallback, $item, $key);
			if (!is_bool($include)) throw new \InvalidArgumentException(__METHOD__ . ' callback must return boolean');

			if ($include) $c->add($item);
		}
		return $c;
	}

	/**
	 * Convenience method to allow looping over items in Collection without reset iterator for the current
	 * Especially helpful with nested recurrences of same collection
	 * @param callable $each
	 * @return void
	 */
	public function each(callable $each): void
	{
		$objs = clone $this;
		foreach($objs as $ix => $obj) {
			call_user_func($each, $obj, $ix);
		}
	}

	/**
	 * Merge collection into existing collection
	 * @param Collection $collection
	 * @return void
	 */
	public function merge(Collection $collection)
	{
		foreach($collection as $item) {
			$this->assertValidItem($item);
			$this->add($item);
		}
	}

	public function createLookup(callable $keyGenerator, callable $valueMapper=null): Dictionary
	{
		$d = new Dictionary();

		$this->each(function($value, $ix) use ($d, $keyGenerator, $valueMapper) {
			$key = call_user_func($keyGenerator, $value, $ix);
			$value = $valueMapper === null ? $value : call_user_func($valueMapper, $value, $ix);
			$d->set($key, $value);
		});

		return $d;
	}

	public function createMultiValueLookup(callable $keyGenerator): Dictionary
	{
		$d = new Dictionary();

		$this->each(function($value) use ($d, $keyGenerator) {
			$key = call_user_func($keyGenerator, $value);
			if (!$d->has($key)) {
				$d->set($key, new static());
			}
			$d->get($key)->add($value);
		});

		return $d;
	}
	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the current element
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return T Can return any type.
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
	 * @return T Can return all value types.
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
		$this->assertValidItem($value);
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
	public function count(): int
	{
		return count($this->data);
	}

	public function __unset($name)
	{
		if ($this->has($name)) {
			unset($this->data[$name]);
		}
	}

	/**
	 * @param $name
	 * @return T
	 */
	public function __get($name)
	{
		return $this->get($name);
	}

	public function __set($index, $value)
	{
		if ($index !== null && !is_numeric($index)) throw new \InvalidArgumentException('Cannot set non-numeric index on ' . __CLASS__);
		$this->assertValidItem($value);

		if ($index === null) $this->data[] = $value;
		else $this->data[$index] = $value;
	}

	public function __isset($name)
	{
		return $this->has($name);
	}

//	/**
//	 * Deep clone of this instance to ensure that nested WebImage\Core\Dictionary's are also cloned.
//	 *
//	 * @return void
//	 */
//	public function __clone()
//	{
//		$array = array();
//
//		foreach ($this->data as $key => $value) {
//			if ($value instanceof static) {
//				$array[$key] = clone $value;
//			} else {
//				$array[$key] = $value;
//			}
//		}
//
//		$this->data = $array;
//	}
}
