<?php

namespace WebImage\Config;

class Configurator extends Config
{
	private $ignoreKeys = [];
	/**
	 * Get and delete a key (so that it will not be used by configure(...)
	 *
	 * @param $key
	 * @param null $default
	 *
	 * @return mixed
	 */
	public function getAndIgnore($key, $default=null)
	{
		$value = parent::get($key, $default);
		$this->ignore($key);

		return $value;
	}

	/**
	 * Marks a config key as "config" only, meaning $this->configure() will not attempt to call any methods on the passed object
	 * @param $key
	 */
	public function ignore($key)
	{
		if (!in_array($key, $this->ignoreKeys)) $this->ignoreKeys[] = $key;
	}

	/**
	 * Configure an object by calling setters for each configuration item
	 * @param $obj
	 */
	public function configure($obj)
	{
		if (!is_object($obj)) {
			throw new \InvalidArgumentException('%s was expecting an object', __METHOD__);
		}

		foreach($this as $key=>$val) {
			if (in_array($key, $this->ignoreKeys)) continue; // Do not process any

			$methods = $this->getPossibleMethods($key);

			foreach($methods as $method) {
				if (method_exists($obj, $method)) {
					call_user_func([$obj, $method], $val);
					continue 2;
				}
			}

			// We should not make it this far
			throw new \RuntimeException(sprintf('Missing "%s" setter on %s (tried %s)', $key, get_class($obj), implode(', ', $methods)));
		}
	}

	/**
	 * Get the possible method names that might exist as a setter
	 *
	 * @param $key
	 *
	 * @return string[]
	 */
	protected function getPossibleMethods($key)
	{
		$methodKey = ucfirst($key);

		return [
			'set' . $methodKey,
			'is' . $methodKey,
			'add' . $methodKey
		];
	}

	/**
	 * Set a value, only if it does not exist
	 * @param string $key
	 * @param mixed $value
	 */
	public function setDefault($key, $value)
	{
		if (!$this->has($key)) $this->set($key, $value);
	}
}