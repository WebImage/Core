<?php

namespace WebImage\Config;

interface CreatableFromConfiguratorInterface
{
	/**
	 * Create an instance of an object from its configuration values
	 * @param Configurator $configurator
	 * @return mixed
	 */
	public static function createFromConfigurator(Configurator $configurator);
}