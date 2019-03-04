<?php

namespace WebImage\Paths;

use League\Container\ServiceProvider\AbstractServiceProvider;
use WebImage\Application\ApplicationInterface;
use WebImage\Config\Config;

class PathManagerServiceProvider extends AbstractServiceProvider
{
	protected $provides = [
		PathManagerInterface::class
	];

	/**
	 * @inheritdoc
	 */
	public function register()
	{
		$paths = $this->getPaths();

		$manager = new PathManager();
		foreach($paths as $path) {
			$manager->add($path);
		}

		$this->getContainer()->share(PathManagerInterface::class, $manager);
	}

	protected function getPaths()
	{
		/** @var ApplicationInterface $app */
		$app = $this->getContainer()->get(ApplicationInterface::class);
		$config = $app->getConfig();
		$paths = isset($config['paths']) ? $config['paths'] : [];
		$paths = $paths instanceof Config ? $paths->toArray() : $paths;

		if (!in_array($app->getProjectPath(), $paths)) {
			$paths[] = $app->getProjectPath();
		}

		// Check the core path as a last result
		$paths[] = $app->getCorePath();

		return $paths;
	}
}