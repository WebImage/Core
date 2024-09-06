<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/20/18
 * Time: 10:21 AM
 */

namespace WebImage\Paths;

interface PathManagerInterface
{
	/**
	 * Add a path to the path manager
	 *
	 * @param string $path
	 */
	public function add(string $path): void;

	/**
	 * Removes a path from the path manager
	 *
	 * @param string $path
	 */
	public function remove(string $path): void;

	/**
	 * Check if path already exists
	 *
	 * @param string $path
	 * @return bool
	 */
	public function has(string $path): bool;

	/**
	 * Retrieve list of paths
	 *
	 * @return string[]
	 */
	public function all(): array;

	/**
	 * Return a new PathManager by appending $path to the existing paths
	 *
	 * @param $path
	 * @return PathManager
	 */
	public function withAppendedPath($path): PathManager;
}