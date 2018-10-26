<?php
/**
 * Keeps track of and loads plugins.
 *
 * Lifecycle:
 * 1. $loader->register($directoryForPlugin)
 * 2. $loader->load($pluginId)
 */
namespace WebImage\Application;

use WebImage\Core\Dictionary;

class PluginLoader
{
	/**
	 * The base path from which relative paths will be appended
	 * @var string
	 */
	private $basePluginDir;
	/**
	 * Plugins that have been registered
	 *
	 * @var Dictionary
	 */
	private $registered;
	/**
	 * The plugins that have been loaded
	 * @var
	 */
	private $loaded;

	/**
	 * PluginLoader constructor.
	 */
	public function __construct($basePluginDir)
	{
		$this->basePluginDir = rtrim($basePluginDir, '/');
		$this->registered = new Dictionary();
		$this->loaded = new Dictionary();
	}
	public function register(PluginInterface $plugin)
	{
//		$r = new \ReflectionObject($plugin);
//		$dir = $rootDir = dirname($r->getFileName());
//
//		while (!file_exists($dir.'/plugin.json')) {
//			if ($dir === dirname($dir)) {
//				throw new \RuntimeException(sprintf('%s is missing the required plugin.json file', $r->getName()));
//			}
//			$dir = dirname($dir);
//		}
//
//		$manifestFile = $dir . '/plugin.json';
//
//		$manifest = new PluginManifest($manifestFile);

		$id = $plugin->getManifest()->getId();

		if ($this->registered->has($id)) {
			throw new PluginRegisteredException(sprintf('The plugin %s has already been registered', $id));
		}

		$this->registered->set($id, $plugin);
	}

	public function load(ApplicationInterface $app)
	{
		/**
		 * @var string $pluginId
		 * @var PluginInterface $plugin
		 */
		foreach($this->registered as $pluginId => $plugin) {
			if ($this->loaded->has($pluginId)) continue;

			foreach($plugin->getManifest()->getRequiredPlugins() as $requiredPluginId) {
				if (!$this->registered->has($requiredPluginId)) {
					throw new PluginNotFoundException(sprintf('Plugin %s is missing required plugin %s', $pluginId, $requiredPluginId));
				}
			}

			$plugin->load($app);
//			$app->getServiceManager()->share($pluginId, $plugin);
		}
	}
}