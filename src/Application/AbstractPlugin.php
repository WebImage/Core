<?php

namespace WebImage\Application;

use WebImage\Config\Config;
use WebImage\Core\Version;
use WebImage\ServiceManager\ServiceManagerConfig;

abstract class AbstractPlugin implements PluginInterface
{
	/**
	 * @var string
	 */
	private $pluginPath;
	/**
	 * @var PluginManifest
	 */
	private $manifest;

	/**
	 * Get the root plugin path
	 * Typically this will be the directory where the plugin.json manifest file exists
	 *
	 * @return string
	 */
	public function getPluginPath()
	{
		if (null === $this->pluginPath) {
			$r = new \ReflectionObject($this);
			$dir = $rootDir = dirname($r->getFileName());

			while (!file_exists($dir.'/plugin.json')) {
				if ($dir === dirname($dir)) {
					throw new \RuntimeException(sprintf('%s is missing the required plugin.json file', $r->getName()));
				}
				$dir = dirname($dir);
			}
			$this->pluginPath = $dir;
		}

		return $this->pluginPath;
	}

	/**
	 * @inheritdoc
	 */
	public function load(ApplicationInterface $app)
	{
		$config = $this->getConfig();

		if (null !== $config && !($config instanceof Config)) {
			throw new \RuntimeException(sprintf('%s was expecting an instance of %s', __METHOD__, Config::class));
		}

		if (null !== $config) {
			$app->getConfig()->merge($config);

			// If serviceManager config key is defined then load those configurations
			if ($config->has(ApplicationInterface::CONFIG_SERVICE_MANAGER)) {
				$serviceConfig = new ServiceManagerConfig($config->get(ApplicationInterface::CONFIG_SERVICE_MANAGER));
				$serviceConfig->configureServiceManager($app->getServiceManager());
			}
		}
	}

	/**
	 * @inheritdoc
	 */
	public function install(ApplicationInterface $app) {}

	/**
	 * @inheritdoc
	 */
	public function uninstall(ApplicationInterface $app) {}

	/**
	 * @inheritdoc
	 */
	public function getConfig() {}

	public function getManifest()
	{
		if (null === $this->manifest) $this->initManifest();

		return $this->manifest;
	}

	protected function initManifest()
	{
		$path = $this->getPluginPath();
		$manifestPath = $path . '/plugin.json';
		$this->manifest = new PluginManifest($manifestPath);
	}
}