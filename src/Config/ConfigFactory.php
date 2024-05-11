<?php

namespace WebImage\Config;

class ConfigFactory
{
	/**
	 * Import a PHP file into an existing config
	 *
	 * @param string $phpFile A glob pattern to search for and import files.  Files must be a PHP file that returns an array of configuration values
	 *
	 * @return Config
	 */
	public static function createFromFile(string $file): Config
	{
		$files = glob($file);
		$config = new Config();

		foreach($files as $file) {
			if (!file_exists($file)) {
				throw new \InvalidArgumentException('File not found: ' . $file);
			}

			$contents = require($file);
			if (!is_array($contents)) {
				throw new \InvalidArgumentException(sprintf('Config file %s must return an array', $file));
			}

			$config->merge(new Config($contents));
		}

		return $config;
	}
}