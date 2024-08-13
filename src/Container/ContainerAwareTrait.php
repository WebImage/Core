<?php

namespace WebImage\Container;

use Psr\Container\ContainerInterface;

trait ContainerAwareTrait
{
	/** @var ContainerInterface */
	private ContainerInterface $container;

	/**
	 * Set the container
	 *
	 * @param ContainerInterface $container
	 */
	public function setContainer(ContainerInterface $container)
	{
		$this->container = $container;
	}

	/**
	 * Get the container
	 *
	 * @return ContainerInterface
	 */
	public function getContainer(): ContainerInterface
	{
		return $this->container;
	}
}