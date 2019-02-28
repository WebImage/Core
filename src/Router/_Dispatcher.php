<?php

namespace WebImage\Router;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebImage\Http\Response;

class Dispatcher extends \League\Route\Dispatcher
{
	/**
	 * @inheritdoc
	 */
//	protected function handleFound(callable $route, array $vars)
//	{

//		$result = parent::handleFound($route, $vars);
//
//		return $result;
//	}
	/**
	 * Create a string response
	 *
	 * @param string $str
	 * @return Response
	 */
//	private function stringResponse(string $str)
//	{
//		$res = new Response();
//		$res->getBody()->write($str);
//
//		return $res;
//	}
//
//	private function jsonResponse(array $json)
//	{
//		$res = new Response();
//		$res->getBody()->write(json_encode($json));
//
//		return $res;
//	}
}