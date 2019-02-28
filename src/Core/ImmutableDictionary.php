<?php

namespace WebImage\Core;

class ImmutableDictionary extends Dictionary
{
	public function merge(Dictionary $merge)
	{
		throw new \RuntimeException('This object cannot be mutated');
	}

	public function offsetSet($offset, $value)
	{
		throw new \RuntimeException('This object cannot be mutated');
	}

	public function offsetUnset($offset)
	{
		throw new \RuntimeException('This object cannot be mutated');
	}

	public function __unset($name)
	{
		throw new \RuntimeException('This object cannot be mutated');
	}

	public function __set($name, $value)
	{
		throw new \RuntimeException('This object cannot be mutated');
	}

	public function set($name, $value)
	{
		throw new \RuntimeException('This object cannot be mutated');
	}

	public function del($name)
	{
		throw new \RuntimeException('This object cannot be mutated');
	}
}