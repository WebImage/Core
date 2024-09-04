<?php

namespace WebImage\Validation\Rules;

use WebImage\Validation\AbstractRule;
use WebImage\Validation\RuleFromArrayFactoryInterface;
use WebImage\Validation\RuleInterface;

class RequiredRule extends AbstractRule implements RuleFromArrayFactoryInterface
{
	protected string $message = '{label} is required';

	protected function doValidation($value): bool
	{
		return !$this->isValueEmpty($value);
	}

	protected function shouldValidateWhenEmpty($value): bool
	{
		return true;
	}

	public static function fromArray(array $rule): RuleInterface
	{
		if (count($rule) > 0) throw new \InvalidArgumentException('RequiredRule expects no parameters');

		return new self();
	}

	public function shouldHaltValidation(): bool
	{
		return true;
	}
}