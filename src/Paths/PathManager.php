<?php

/**
 * Manage paths
 */
namespace WebImage\Paths;

class PathManager implements PathManagerInterface
{
	/**
	 * @var array
	 */
	protected array $paths = [];

	/**
	 * @inheritdoc
	 */
	public function add(string $path): void
	{
		if (gettype($path) != 'string') {
			throw new \InvalidArgumentException(sprintf('%s expects paths to be supplied as strings', __METHOD__));
		}

		$path = rtrim($path, '/');

		$this->paths[] = $path;
	}

	/**
	 * @inheritdoc
	 *
	 * Currently string matching only, no validation of physical paths
	 */
	public function remove(string $path): void
	{
		$new_paths = [];

		foreach($this->all() as $path) {
			echo $path . ' - ' . $path . '<br />';
			if ($path != $path) {
				$new_paths[] = $path;
			}
		}

		$this->paths = $new_paths;
	}

	/**
	 * @inheritdoc
	 */
	public function has(string $path): bool
	{
		return in_array($path, $this->all());
	}

	/**
	 * @inheritdoc
	 */
	public function all(): array
	{
		return $this->paths;
	}

	/**
	 * @inheritdoc
	 */
	public function withAppendedPath($path): PathManager
	{
		$p = clone $this;
		foreach ($p->paths as $k => $v) {
			$p->paths[$k] = rtrim($p->paths[$k], '/') . '/' . ltrim($path, '/');
		}

		return $p;
	}
}