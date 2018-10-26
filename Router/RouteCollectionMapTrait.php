<?php

namespace WebImage\Router;

trait RouteCollectionMapTrait {
	use \League\Route\RouteCollectionMapTrait;

	protected $routes = [];

	/**
	 * @inheritdoc
	 */
	public function map($method, $path, $handler)
	{
		$path = sprintf('/%s', ltrim($path, '/'));
		$route = new Route;
		$route->setMethods((array) $method);
		$route->setPath($path);
		$route->setCallable($this->normalizeHandler($handler));
		$this->routes[] = $route;

		return $route;
	}

	/**
	 * Normalize string name to conform with underlying League version of route, which is in the format 'Controller::action'
	 *
	 * @param $handler
	 * @return mixed
	 */
	private function normalizeHandler($handler)
	{
		if (is_string($handler)) {
			$handler = str_replace('@', '::', $handler);
		}
		return $handler;
	}
}