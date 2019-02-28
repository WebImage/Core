<?php

namespace WebImage\Router;

class RouteHelper {
	/**
	 * Normalize string name to conform with underlying League version of route, which is in the format 'Controller::action'
	 *
	 * @param mixed $handler
	 * @return mixed
	 */
	public static function normalizeHandler($handler)
	{
		if (is_string($handler)) {
			$handler = str_replace('@', '::', $handler);
		}

		return $handler;
	}
}