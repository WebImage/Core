<?php

namespace WebImage\Validation;

abstract class AbstractRule implements RuleInterface
{
	protected string $message = 'Invalid';
	protected bool $haltValidation = false;

	public function validate($value): bool
	{
		if (!$this->shouldValidateWhenEmpty($value) && $this->isValueEmpty($value)) return true;

		return $this->doValidation($value);
	}

	abstract protected function doValidation($value): bool;

	protected function shouldValidateWhenEmpty($value): bool
	{
		return false;
	}

	protected function isValueEmpty($value): bool
	{
		if ($value === null) return true;
		else if (is_string($value) && strlen($value) == 0) return true;
		else if (is_array($value) && count($value) == 0) return true;

		return false;
	}

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