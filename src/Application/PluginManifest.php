<?php

namespace WebImage\Application;

use WebImage\Core\Version;

class PluginManifest
{
	/** @var PluginAuthor[] */
	private $authors = [];
	/** @var string */
	private $id;
	/** @var string */
	private $name;
	/** @var Version */
	private $version;
	/** @var string */
	private $description = '';
	/** @var string $root Path to plugin root */
	private $root;
	private $requiredPlugins = [];

	public function __construct($manifestFile)
	{
		if (!file_exists($manifestFile)) {
			throw new \RuntimeException('Missing required plugin.json');
		}

		$manifestStr = file_get_contents($manifestFile);
		$manifest = empty($manifestStr) ? [] : json_decode($manifestStr, true);

		foreach(['id', 'name', 'version'] as $requiredVar) {
			if (!isset($manifest[$requiredVar])) {
				throw new \RuntimeException(sprintf('%s is a required plugin.json field in %s', $requiredVar, $manifestFile));
			}
		}

		$this->root = dirname($manifestFile);
		$this->id = $manifest['id'];
		$this->name = $manifest['name'];
		$this->description = isset($manifest['description']) ? $manifest['description'] : '';
		$this->version = Version::createFromString($manifest['version']);
		$this->loadRequirements($manifest);
	}

	private function loadRequirements(array $manifest)
	{
		if (!isset($manifest['requirements'])) return;
		$this->loadRequiredPlugins($manifest['requirements']);
	}

	private function loadRequiredPlugins($requirements)
	{
		if (!isset($requirements['plugins'])) return;

		foreach($requirements['plugins'] as $plugin) {
			$this->requiredPlugins[] = $plugin;
		}
	}

	/**
	 * @return PluginAuthor[]
	 */
	public function getAuthors()/*: array*/
	{
		return $this->authors;
	}

	/**
	 * @return string
	 */
	public function getId()/*: string*/
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getName()/*: string*/
	{
		return $this->name;
	}

	/**
	 * @return Version
	 */
	public function getVersion()/*: Version */
	{
		return $this->version;
	}

	/**
	 * @return string
	 */
	public function getDescription()/*: string */
	{
		return $this->description;
	}

	/**
	 * @return string
	 */
	public function getRoot()/*: string*/
	{
		return $this->root;
	}

	/**
	 * @return array
	 */
	public function getRequiredPlugins()/*: array*/
	{
		return $this->requiredPlugins;
	}
}