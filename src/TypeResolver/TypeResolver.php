<?php

namespace WebImage\TypeResolver;

use WebImage\Config\Configurator;
use WebImage\Config\CreatableFromConfiguratorInterface;

class TypeResolver
{
	/** @var array key => className */
	private $typeMap = [];
	private $typeConfigurator = [];

	/**
	 * Gets an instantiated element by name (if an ElementInterface is passed directly it will be returned as is)
	 *
	 * @param string String reference to a class, as defined by $typeMap
	 * @param array|Configurator|null An associative array or Configurator of options
	 *
	 * @return object
	 * @throws \Exception
	 */
	public function resolve(string $key, $configurator = null)
	{
		if (!$this->has($key)) {
			throw new \Exception('Invalid element: ' . $key);
		}

		$class = $this->getClass($key);

		// If a configurator was registered, use it
		if (null === $configurator && isset($this->typeConfigurator[$key])) {
			$configurator = $this->typeConfigurator[$key];
		}

		if (null === $configurator || is_array($configurator)) {
			$configurator = new Configurator(null === $configurator ? [] : $configurator);
		}

		if (!class_exists($class)) {
			throw new \RuntimeException(sprintf('The class %s for type %s does not exist', $key, $class));
		} else if (!is_a($class, CreatableFromConfiguratorInterface::class, true)) {
			throw new \RuntimeException(sprintf('The type %s of class %s must be resolved must be of type %s', $key, $class, CreatableFromConfiguratorInterface::class));
		}

		return $class::createFromConfigurator($configurator);
	}

	/**
	 * Register a class name and key
	 *
	 * @param string $class A resolvable class that will be associated with key
	 * @param string|null $key A key that the class will be associated with (key will be generated if not supplied)
	 * @param array|Configurator|null $configurator An associative array or Configurator of options
	 *
	 * @return void
	 */
	public function register($class, $key, $configurator = null)
	{
		if (null === $key || empty($key)) {
			$parts = explode('\\', $class);
			$key = $this->normalizeKey(array_pop($parts));
		}

		if ($this->has($key)) {
			throw new \RuntimeException(sprintf('A class is already registered for this key: %s', $key));
		}

		if (null !== $configurator) $this->typeConfigurator[$key] = $configurator;

		$this->typeMap[$key] = $class;
	}

	/**
	 * Unregiser a class name or key
	 *
	 * @param string $key The class name or key to unregister (class name is converted to key)
	 *
	 * @return void
	 */
	public function unregister($key)
	{
		$parts = explode('\\', $key);
		$key = $this->normalizeKey(array_pop($parts));

		if (!isset($this->typeMap[$key])) {
			throw new \RuntimeException(sprintf('%s is not a registered element', $key));
		}
		// Remove any configurators that were setup
		if (issset($this->typeConfigurator[$key])) unset($this->typeConfigurator[$key]);

		unset($this->typeMap[$key]);
	}

	/**
	 * Gets the class name for a given key
	 * @param string $key Can be a class name used to register a class, or its associated key
	 * @return ElementInterface
	 * @throws \InvalidArgumentException When a class is not defined for a key
	 */
	public function getClass($key)
	{
		$key = $this->normalizeKey($key);

		if (!$this->has($key)) {
			throw new \InvalidArgumentException(sprintf('A class is not defined for the key: %s', $key));
		}

		return $this->typeMap[$key];
	}

	/**
	 * Checks whether an element exists by key
	 *
	 * @param string $key Can be a class name used to register a class, or its associated key
	 * @return bool
	 */
	public function has($key)
	{
		$key = $this->normalizeKey($key);

		return isset($this->typeMap[$key]);
	}

	/**
	 * Gets copy of type map
	 * @return array
	 */
	public function getTypeMap()
	{
		return $this->typeMap;
	}

	/**
	 * Generate key to be used for registration
	 *
	 * @param $key
	 * @return string
	 */
	private function normalizeKey($key)
	{
		$parts = explode('\\', $key);
		$key = array_pop($parts);

		$base = strtolower(substr($key, 0, 1)) . substr($key, 1);

		return strtolower(preg_replace('/([A-Z]+)/', '-$1', $base));
	}
}
