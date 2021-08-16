<?php

namespace WebImage\Event;

use WebImage\Container\ServiceProvider\AbstractServiceProvider;

class EventServiceProvider extends AbstractServiceProvider
{
	protected $provides = [
		ManagerInterface::class
	];

	public function register(): void
	{
		$this->getContainer()->addShared(ManagerInterface::class, Manager::class);
	}
}