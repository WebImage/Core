<?php

namespace WebImage\Event;

use WebImage\Container\ServiceProvider\AbstractServiceProvider;

class EventServiceProvider extends AbstractServiceProvider
{
	protected $provides = [
		EventManagerInterface::class
	];

	public function register(): void
	{
		$this->getContainer()->addShared(EventManagerInterface::class, EventManager::class);
	}
}
