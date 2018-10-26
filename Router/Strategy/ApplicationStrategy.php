<?php

namespace WebImage\Router\Strategy;

use Exception;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use League\Route\Route;
use League\Route\Strategy\ApplicationStrategy as BaseApplicationStrategy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebImage\Controllers\ControllerInterface;

class ApplicationStrategy extends BaseApplicationStrategy implements ContainerAwareInterface {
	use ContainerAwareTrait;

	/**
	 * @inheritdoc
	 */
	public function getCallable(Route $route, array $vars)
	{
		return function (ServerRequestInterface $request, ResponseInterface $response, callable $next) use ($route, $vars)
		{
			$request = $this->injectVarsAsAttributes($request, $vars);

			list($request, $callable) = $this->preProcessCallable($request, $route);

			$response = call_user_func($callable, $request, $response);

			if ($response instanceof ResponseInterface) {
				return $next($request, $response);
			}

			return $response;
		};
	}

	/**
	 * Injects vars into the request as attributes
	 *
	 * @param ServerRequestInterface $request
	 * @param array $vars
	 * @return ServerRequestInterface|static
	 */
	protected function injectVarsAsAttributes(ServerRequestInterface $request, array $vars)
	{
		foreach($vars as $key => $val) {
			$request = $request->withAttribute($key, $val);
		}

		return $request;
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param Route $route
	 *
	 * @return array(ServerRequestInterface, Callable)
	 */
	private function preProcessCallable(ServerRequestInterface $request, Route $route)
	{
		$callable = $route->getCallable();

		if (is_array($callable) && isset($callable[0]) && is_object($callable[0])) {
			if ($callable[0] instanceof ControllerInterface) {
				$request = $request->withAttribute(ControllerInterface::DISPATCH_ACTION_ATTRIBUTE, $callable[1]);
				$callable[1] = ControllerInterface::DISPATCH_METHOD;
			}
			if ($callable[0] instanceof ContainerAwareInterface) {
				$callable[0]->setContainer($this->getContainer());
			}
		}

		return [$request, $callable];
	}

	/**
	 * @inheritdoc
	 */
	public function getExceptionDecorator(Exception $exception)
	{
		return function(ServerRequestInterface $req, ResponseInterface $res, \Closure $next) use ($exception) {
			$res = $this->exceptionResponse($exception, $res);

			$res->getBody()->write($exception->getMessage());

			return $next($req, $res);
		};
	}

	/**
	 * Modify the response if the exception is an Http exception
	 *
	 * @param Exception $exception
	 * @param ResponseInterface $res
	 * @return ResponseInterface|static
	 */
	private function exceptionResponse(Exception $exception, ResponseInterface $res)
	{
		if ($exception instanceof \League\Route\Http\Exception\HttpExceptionInterface) {
			$res = $res->withStatus($exception->getStatusCode());

			foreach($exception->getHeaders() as $name => $value) {
				$res = $res->withHeader($name, $value);
			}
		}

		return $res;
	}
}