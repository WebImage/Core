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
	public function add($path);

	/**
	 * Removes a path from the path manager
	 *
	 * @param string $path
	 */
	public function remove($path);

	/**
	 * Check if path already exists
	 *
	 * @param string $path
	 * @return bool
	 */
	public function has($path);

	/**
	 * Retrieve list of paths
	 *
	 * @return array
	 */
	public function all();

	/**
	 * Return a new PathManager by appending $path to the existing paths
	 *
	 * @param $path
	 * @return PathManager
	 */
	public function withAppendedPath($path);
}