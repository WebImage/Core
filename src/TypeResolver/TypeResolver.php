<?php

namespace WebImage\TypeResolver;

use Exception;
use InvalidArgumentException;
use RuntimeException;
use WebImage\Config\Configurator;
use WebImage\Config\CreatableFromConfiguratorInterface;

class TypeResolver
{
	/** @var array key => className */
	private array $typeMap          = [];
	private array $typeConfigurator = [];

	/**
	 * Gets an instantiated element by name (if an ElementInterface is passed directly it will be returned as is)
	 *
	 * @param string $key String reference to a class, as defined by $typeMap
	 * @param array|Configurator|null An associative array or Configurator of options
	 *
	 * @return object
	 * @throws Exception
	 */
	public function resolve(string $key, $configurator = null)
	{
		if (!$this->has($key)) throw new Exception('Invalid element: ' . $key);

		$class = $this->getClass($key);

		// If a configurator was registered, use it
		if (null === $configurator && isset($this->typeConfigurator[$key])) {
			$configurator = $this->typeConfigurator[$key];
		}

		if (null === $configurator || is_array($configurator)) {
			$configurator = new Configurator(null === $configurator ? [] : $configurator);
		}

		if (!class_exists($class)) {
			throw new RuntimeException(sprintf('The class %s for type %s does not exist', $key, $class));
		}

		$instance = $this->createInstance($class, $configurator);
		if ($instance instanceof KeyResolvedTypeInterface) $instance->setResolvedTypeKey($key);

		return $instance;
	}

	/**
	 * Create an instance of the requested class
	 *
	 * @param string $class
	 * @param Configurator $configurator
	 *
	 * @return mixed
	 */
	protected function createInstance(string $class, Configurator $configurator)
	{
		return is_a($class, CreatableFromConfiguratorInterface::class, true) ? $class::createFromConfigurator($configurator) : new $class;
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
	public function register(string $class, ?string $key, $configurator = null)
	{
		if ($key === null || empty($key)) {
			$parts = explode('\\', $class);
			$key = array_pop($parts);
		}

		$key = $this->normalizeKey($key);

		if ($this->has($key)) {
			throw new RuntimeException(sprintf('A class is already registered for this key: %s', $key));
		}

		if (null !== $configurator) $this->typeConfigurator[$key] = $configurator;

		$this->typeMap[$key] = $class;
	}

	/**
	 * Unregister a class name or key
	 *
	 * @param string $key The class name or key to unregister (class name is converted to key)
	 *
	 * @return void
	 */
	public function unregister(string $key)
	{
		$parts = explode('\\', $key);
		$key = $this->normalizeKey(array_pop($parts));

		if (!isset($this->typeMap[$key])) {
			throw new RuntimeException(sprintf('%s is not a registered element', $key));
		}
		// Remove any configurators that were setup
		if (isset($this->typeConfigurator[$key])) unset($this->typeConfigurator[$key]);

		unset($this->typeMap[$key]);
	}

	/**
	 * Gets the class name for a given key
	 * @param string $key Can be a class name used to register a class, or its associated key
	 * @return mixed
	 * @throws InvalidArgumentException When a class is not defined for a key
	 */
	public function getClass(string $key)
	{
		$key = $this->normalizeKey($key);

		if (!$this->has($key)) {
			throw new InvalidArgumentException(sprintf('A class is not defined for the key: %s', $key));
		}

		return $this->typeMap[$key];
	}

	/**
	 * Checks whether an element exists by key
	 *
	 * @param string $key Can be a class name used to register a class, or its associated key
	 * @return bool
	 */
	public function has(string $key): bool
	{
		$key = $this->normalizeKey($key);

		return isset($this->typeMap[$key]);
	}

	/**
	 * Gets copy of type map
	 * @return array
	 */
	public function getTypeMap(): array
	{
		return $this->typeMap;
	}

	/**
	 * Generate key to be used for registration
	 *
	 * @param $key
	 * @return string
	 */
	private function normalizeKey($key): string
	{
		$parts = explode('\\', $key);
		$key = array_pop($parts);

		$base = strtolower(substr($key, 0, 1)) . substr($key, 1);

		return strtolower(preg_replace('/([A-Z]+)/', '-$1', $base));
	}
}
