<?php

namespace WebImage\Event;

interface EventManagerInterface
{
	const HIGH_PRIORITY = 1000;
	const MEDIUM_PRIORITY = 500;
	const LOW_PRIORITY = 0;

	/**
	 * Listen for an event
	 * @param string $event
	 * @param callable $handler With a function/method signature of $handler(Event $event)
	 * @param int $priority
	 *
	 * @return void
	 */
	public function listen(string $event, callable $handler, int $priority = self::MEDIUM_PRIORITY): void;

	/**
	 * @param string|Event $event
	 * @param mixed $data
	 * @param mixed|null $sender
	 *
	 * @return mixed[] Responses from all listeners
	 */
	public function trigger($event, $data, ?object $sender = null): array;
}
