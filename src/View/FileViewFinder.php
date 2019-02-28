<?php

namespace WebImage\View;

use RuntimeException;
use WebImage\Paths\PathManager;

class FileViewFinder implements ViewFinderInterface {
	private static $EXCLUDE_PROFILES_CHAR = '!';
	/**
	 * @var string[]
	 */
	protected $extensions = [];
	/**
	 * @var string[]
	 */
	protected $profiles = [];
	/**
	 * @var PathManager
	 */
	protected $paths;
	/**
	 * @var string[] Cache of previously found views
	 */
	protected $views;

	public function __construct(PathManager $paths)
	{
		$this->paths = $paths;
	}

	/**
	 * @inheritdoc
	 */
	public function find($view)
	{
		if (isset($this->views[$view])) {
			return $this->views[$view];
		}

		$files = $this->getPossibleFilePaths($view);

		return $this->views[$view] = $this->firstExistingFile($files, $view);
	}

	private function firstExistingFile($files, $view)
	{
		foreach ($files as $file) {
			if (file_exists($file)) {
				return $file;
			}
		}
	}

	/**
	 * @inheritdoc
	 */
	public function addPath($location)
	{
		$this->paths->add($location);
	}

	/**
	 * @inheritdoc
	 */
	public function addVariation($variation)
	{
		$this->profiles[] = $variation;
	}

	/**
	 * @inheritdoc
	 */
	public function addExtension($extension)
	{
		if (($index = array_search($extension, $this->extensions)) !== false) {
			unset($this->extensions[$index]);
		}

		array_unshift($this->extensions, $extension);
	}

	public function getScoredPossiblePaths($view)
	{
		$views = array();
		$exclude_profiles = substr($view, -1) == self::$EXCLUDE_PROFILES_CHAR;
		if ($exclude_profiles) {
			$view = substr($view, 0, -1);
		}

		foreach($this->paths->all() as $path) {
			$base = $path . '/' . $view;

			if (!$exclude_profiles) {
				foreach ($this->profiles as $profile) {
					foreach ($this->extensions as $extension) {
						$views[] = sprintf('%s~%s.%s', $base, $profile, $extension);
					}
				}
			}

			foreach($this->extensions as $extension) {
				$views[] = sprintf('%s.%s', $base, $extension);
			}
		}

		return $views;
	}

	public function getPossibleFilePaths($view)
	{
		return $this->getScoredPossiblePaths($view);
	}
}