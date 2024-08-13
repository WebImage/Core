<?php

namespace WebImage\Validation;

abstract class AbstractRule implements RuleInterface
{
	protected string $message = 'Invalid';
	protected bool $haltValidation = false;

	abstract public function validate($value): bool;

	public function getMessage(): string
	{
		return $this->message;
	}

	public function setMessage(string $message): void
	{
		$this->message = $message;
	}

	/**
	 * Return values that can be used in a template
	 * @return string[]
	 */
	public function toArray(): array
	{
		return [
			'label' => 'Value'
		];
	}

	/**
	 * @return bool
	 */
	public function shouldHaltValidation(): bool
	{
		return $this->haltValidation;
	}

	public function setHaltValidation(bool $haltValidation): void
	{
		$this->haltValidation = $haltValidation;
	}
}