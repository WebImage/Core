<?php

namespace WebImage\Validation;

use WebImage\Validation\Rules\AcceptedRule;
use WebImage\Validation\Rules\ArrayRule;
use WebImage\Validation\Rules\BooleanRule;
use WebImage\Validation\Rules\EmailRule;
use WebImage\Validation\Rules\EqualsFieldValueRule;
use WebImage\Validation\Rules\EqualsRule;
use WebImage\Validation\Rules\IntegerRule;
use WebImage\Validation\Rules\LengthMaxRule;
use WebImage\Validation\Rules\LengthMinRule;
use WebImage\Validation\Rules\LengthRule;
use WebImage\Validation\Rules\MaxRule;
use WebImage\Validation\Rules\MinRule;
use WebImage\Validation\Rules\NumericRule;
use WebImage\Validation\Rules\RequiredRule;

class RuleResolver
{
	private array $typeMap = [];

	public function addRule(string $name, string $class)
	{
		if (in_array($name, Validator::getReservedRuleNames())) throw new \InvalidArgumentException('Rule name is reserved: ' . $name);
		$this->typeMap[$name] = $class;
	}

	public function hasRule(string $name): bool
	{
		return isset($this->typeMap[$name]);
	}

	public function resolve(string $name, array $data): RuleInterface
	{
		if (!isset($this->typeMap[$name])) {
			throw new \InvalidArgumentException('Rule not found: ' . $name);
		}

		return $this->resolveFromClassName($this->typeMap[$name], $data);
	}

	protected function resolveFromClassName(string $class, array $data): RuleInterface
	{
		if (!class_exists($class)) {
			throw new \InvalidArgumentException('Rule class not found: ' . $class);
		}

		if (is_a($class, RuleFromArrayFactoryInterface::class, true)) {
			return $class::fromArray($data);
		}

		return new $class;
	}
}