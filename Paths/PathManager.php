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
	protected $paths = [];

	/**
	 * @inheritdoc
	 */
	public function add($path)
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
	public function remove($remove_path)
	{
		$new_paths = [];

		foreach($this->all() as $path) {
			echo $path . ' - ' . $remove_path . '<br />';
			if ($path != $remove_path) {
				$new_paths[] = $path;
			}
		}

		$this->paths = $new_paths;
	}

	/**
	 * @inheritdoc
	 */
	public function has($path)
	{
		return in_array($path, $this->all());
	}

	/**
	 * @inheritdoc
	 */
	public function all()
	{
		return $this->paths;
	}

	/**
	 * @inheritdoc
	 */
	public function withAppendedPath($path)
	{
		$p = clone $this;
		foreach ($p->paths as $k => $v) {
			$p->paths[$k] = rtrim($p->paths[$k], '/') . '/' . ltrim($path, '/');
		}

		return $p;
	}
}