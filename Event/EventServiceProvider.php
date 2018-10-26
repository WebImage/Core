<?php

namespace WebImage\Event;

use League\Container\ServiceProvider\AbstractServiceProvider;

class EventServiceProvider extends AbstractServiceProvider
{
	protected $provides = [
		ManagerInterface::class
	];

	public function register()
	{
		$this->getContainer()->share(ManagerInterface::class, Manager::class);
	}
}