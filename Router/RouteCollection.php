<?php

namespace WebImage\Router;

use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use WebImage\Router\Strategy\ApplicationStrategy;

class RouteCollection extends \League\Route\RouteCollection implements RouteCollectionInterface, ContainerAwareInterface {
	use ContainerAwareTrait;
	use RouteCollectionMapTrait;

	/**
	 * Use our own strategy, unless one was explicitly set
	 *
	 * @return \League\Route\Strategy\StrategyInterface|ApplicationStrategy
	 */
	public function getStrategy()
	{
		$strategy = parent::getStrategy();

		if (null === $strategy) {

			$strategy = new ApplicationStrategy();

			if ($strategy instanceof ContainerAwareInterface) {
				$strategy->setContainer($this->getContainer());
			}

			$this->setStrategy($strategy);
		}

		return $strategy;
	}
}