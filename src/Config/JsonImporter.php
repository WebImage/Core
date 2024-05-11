<?php

namespace WebImage\Config;

class JsonImporter
{
	/**
	 * Import a JSON string into an existing config
	 *
	 * @param Config $config
	 * @param $str
	 *
	 * @return Config
	 */
	public static function importFromString($str): Config
	{
		if (!is_string($str)) {
			throw new \InvalidArgumentException(sprintf('%s was expecting a string value', __METHOD__));
		}

		$json = json_decode($str, true);

		return new Config($json);
	}
}