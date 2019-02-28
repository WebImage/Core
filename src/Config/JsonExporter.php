<?php

namespace WebImage\Config;

class JsonExporter
{
	/**
	 * Export config to JSON encoded string
	 *
	 * @param Config $config
	 * @param $str
	 *
	 * @return Config
	 */
	public static function exportToString(Config $config)
	{
		return json_encode($config->toArray());
	}
}