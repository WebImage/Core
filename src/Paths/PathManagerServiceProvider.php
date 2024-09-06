<?php

namespace WebImage\Paths;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use WebImage\Application\ApplicationInterface;
use WebImage\Config\Config;
use WebImage\Container\ServiceProvider\AbstractServiceProvider;

class PathManagerServiceProvider extends AbstractServiceProvider
{
	protected array $provides = [
		PathManagerInterface::class
	];

	/**
	 * @throws ContainerExceptionInterface
	 * @throws NotFoundExceptionInterface
	 */
	public function register(): void
	{
		$paths = $this->getPaths();

		$manager = new PathManager();
		foreach($paths as $path) {
			$manager->add($path);
		}

		$this->getContainer()->addShared(PathManagerInterface::class, $manager);
	}

	/**
	 * @throws ContainerExceptionInterface
	 * @throws NotFoundExceptionInterface
	 */
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