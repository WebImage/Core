<?php

namespace WebImage\Container\ServiceProvider;

use WebImage\Application\ApplicationInterface;
use WebImage\Config\Config;

abstract class AbstractServiceProvider extends \League\Container\ServiceProvider\AbstractServiceProvider
{
	protected array $provides = [];

	public function provides(string $id): bool
	{
		return in_array($id, $this->provides);
	}

	/**
	 * Convenience method for returning application configuration
	 * @return Config
	 */
	public function getApplicationConfig(): Config
	{
		return $this->getApplication()->getConfig();
	}
	/**
	 * Convenience method for returning application instance
	 * @return ApplicationInterface
	 */
	public function getApplication(): ApplicationInterface
	{
		return $this->getContainer()->get(ApplicationInterface::class);
	}
}