<?php

namespace WebImage\Container;

use League\Container\ContainerAwareInterface;
use League\Container\Definition\DefinitionFactoryInterface;
use League\Container\Inflector\InflectorAggregateInterface;
use League\Container\ServiceProvider\ServiceProviderAggregateInterface;

class Container extends \League\Container\Container {
	protected function getFromThisContainer($alias, array $args = [])
	{
		$val = parent::getFromThisContainer($alias, $args);

		if ($val instanceof ContainerAwareInterface) {
			$val->setContainer($this);
		}

		return $val;
	}
}