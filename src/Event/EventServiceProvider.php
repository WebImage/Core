<?php

namespace WebImage\Event;

use WebImage\Container\ServiceProvider\AbstractServiceProvider;

class EventServiceProvider extends AbstractServiceProvider
{
	protected array $provides = [
		EventManagerInterface::class
	];

	public function register(): void
	{
		$this->getContainer()->addShared(EventManagerInterface::class, EventManager::class);
	}
}
