<?php

namespace WebImage\Container;

use League\Container\Container as BaseContainer;
use Psr\Container\ContainerInterface;
use League\Container\ContainerAwareInterface;

class Container extends BaseContainer {
	/**
	 * @inheritDoc
	 */
	public function get($id)
	{
		$instance = parent::get($id);

		if ($instance instanceof ContainerAwareInterface) {
			$instance->setContainer($this);
		}

		return $instance;
	}
}