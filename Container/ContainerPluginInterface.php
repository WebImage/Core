<?php

namespace WebImage\Container;

interface ContainerPluginInterface {
	/**
	 * Registers a series of services or service providers with a container
	 *
	 * @param Container $container
	 * @return void
	 */
	public function register(Container $container);
}