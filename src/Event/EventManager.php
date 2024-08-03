<?php

namespace WebImage\Event;

class EventManager implements EventManagerInterface
{
	private array $listeners = [];

	/**
	 * @inheritDoc
	 */
	public function listen(string $event, callable $handler, int $priority = EventManagerInterface::MEDIUM_PRIORITY): void
	{
		$this->listeners[$event][$priority][] = $handler;
	}

	/**
	 * Trigger an event
	 * @param string|Event $event
	 * @param mixed $data
	 * @param mixed|null $sender
	 *
	 * @return mixed[] Responses from all listeners
	 */
	public function trigger($event, $data, object $sender = null): array
	{
		$responses = [];
		$event = $this->buildEvent($event, $data, $sender);
		$listeners = $this->prioritizedListenersForEvent($event);

		foreach($listeners as $listener) {
			$responses[] = call_user_func($listener, $event);

			if ($event->isCancelled()) break;
		}

		return $responses;
	}

	/**
	 * Sort listeners by priority and merge into a single set of listeners
	 * @param Event $event
	 *
	 * @return array
	 */
	private function prioritizedListenersForEvent(Event $event): array
	{
		$type = $event->getType();
		$listeners = $this->listeners[$type] ?? [];

		return count($listeners) == 0 ? [] : call_user_func_array('array_merge', $listeners);
	}

	/**
	 * Create an event object, if $event is not already an event instance
	 * @param string|Event $event
	 * @param mixed $data
	 * @param mixed $sender
	 *
	 * @return Event
	 */
	private function buildEvent(string $event, $data, object $sender = null): Event
	{
		if (is_object($event)) {
			if (!($event instanceof Event)) {
				throw new \InvalidArgumentException(sprintf('%s was expecting an instance of %s', __METHOD__, Event::class));
			}
			if (null !== $data || null !== $sender) {
				throw new \InvalidArgumentException(sprintf('$data and $sender should not be specified when an %s instance is supplied', Event::class));
			}

			return $event;

		} else if (!is_string($event)) {
			throw new \InvalidArgumentException(sprintf('%s was expecting a string event', __METHOD__));
		}

		return new Event($event, $data, $sender);
	}
}
