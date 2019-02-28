<?php

namespace WebImage\Application;

use WebImage\Config\Config;
use WebImage\Core\Dictionary;
use WebImage\Event\EventServiceProvider;
use WebImage\Paths\PathManagerServiceProvider;
use WebImage\ServiceManager\ServiceManager;
use WebImage\ServiceManager\ServiceManagerInterface;
use WebImage\ServiceManager\ServiceManagerAwareTrait;
use WebImage\ServiceManager\ServiceManagerConfig;

abstract class AbstractApplication implements ApplicationInterface
{
	use ServiceManagerAwareTrait;

	/** @var Config $config */
	private $config;
	/** @var PluginLoader $plugins */
	private $plugins;
	/** @var String $projectPath The path to the project home files */
	private $projectPath;
	/**
	 * AbstractApplication constructor.
	 *
	 * @param Config $config
	 * @param ServiceManagerInterface $serviceManager
	 */
	public function __construct(Config $config, ServiceManagerInterface $serviceManager)
	{
		$this->plugins = new PluginLoader($this->getProjectPath());
		$this->setConfig($config);
		$this->setServiceManager($serviceManager);

		// Register this app instance with the service manager
		$serviceManager->share(ApplicationInterface::class, $this);
	}

	/**
	 * @inheritdoc
	 */
	public function run() {
		$this->plugins->load($this);
	}

	/**
	 * @inheritdoc
	 */
	public function get($id)
	{
		return $this->getServiceManager()->get($id);
	}

	/**
	 * @inheritdoc
	 */
	public function has($id)
	{
		return $this->getServiceManager()->has($id);
	}

	/**
	 * @return Config
	 */
	public function getConfig()
	{
		return $this->config;
	}

	/**
	 * @param Config $config
	 */
	protected function setConfig(Config $config)
	{
		$this->config = $config;
	}

	/**
	 * @inheritdoc
	 */
	public function getServiceManager()
	{
		return $this->serviceManager;
	}

	/**
	 * @inheritdoc
	 */
	public function registerPlugin(PluginInterface $plugin)
//	public function registerPlugin($pluginDir)
	{
//		$this->plugins->register($pluginDir);
		$this->plugins->register($plugin);
	}

	/**
	 * Create a fully executable application
	 *
	 * @param Config $config
	 * @return static
	 */
	public static function create(Config $config=null)
	{
		$config = static::mergeConfigWithDefaults($config);

		$serviceManagerConfig = isset($config[self::CONFIG_SERVICE_MANAGER]) ? $config[self::CONFIG_SERVICE_MANAGER] : new Config(); // new ServiceManagerConfig());

		$serviceManager = new ServiceManager(
			new ServiceManagerConfig($serviceManagerConfig)
		);

		$app = new static($config, $serviceManager);

		return $app;
	}

	/**
	 * Gets the application root dir (path of the project's composer file). (Thanks Symfony)
	 *
	 * @author Fabien Potencier <fabien@symfony.com>
	 * @return string The project root dir
	 */
	public function getProjectPath()
	{
		if (null === $this->projectPath) {
			$r = new \ReflectionObject($this);
			$dir = $rootDir = dirname($r->getFileName());

			$composerFiles = [];
			while ($dir !== dirname($dir)) {
				if (file_exists($dir . '/composer.json')) $composerFiles[] = $dir;
				$dir = dirname($dir);
			}

			$this->projectPath = count($composerFiles) == 0 ? $rootDir : array_pop($composerFiles) . '/app';
		}

		return $this->projectPath;
	}


	/**
	 * Merge the provided config with defaults (overwrites defaults)
	 *
	 * @param Config $appConfig
	 * @return Config
	 */
	private static function mergeConfigWithDefaults(Config $appConfig=null)
	{
		$config = new Config(static::getDefaultConfig());

		if ($appConfig instanceof Config) {
			$config->merge($appConfig);
		}

		return $config;
	}

	/**
	 * Default configuration
	 *
	 * @return array
	 */
	protected static function getDefaultConfig()
	{
		return [
			self::CONFIG_SERVICE_MANAGER => static::getDefaultServiceManagerConfig()
		];
	}

	protected static function getDefaultServiceManagerConfig()
	{
		return [
			ServiceManagerConfig::PROVIDERS => [
				PathManagerServiceProvider::class,
				EventServiceProvider::class
			]
		];
	}
}