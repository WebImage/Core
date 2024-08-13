<?php

namespace WebImage\Container;

use Psr\Container\ContainerInterface;

interface ContainerAwareInterface
{
	/**
	 * Set the container
	 *
	 * @param ContainerInterface $container
	 */
	public function setContainer(ContainerInterface $container);
	public function getContainer(): ContainerInterface;
}