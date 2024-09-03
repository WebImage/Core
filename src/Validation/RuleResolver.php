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
	private array $ruleClassMap  = [];
	/** @var array<RuleProvider> */
	private array $ruleProviders = [];

	public function __construct()
	{
		$this->initRules();
	}

	protected function initRules(): void
	{
	}

	public function addRule(string $name, string $class)
	{
		$this->assertValidRuleName($name);
		$this->ruleClassMap[$name] = $class;
	}

	public function addRuleProvider(RuleProvider $provider): void
	{
		$this->ruleProviders[] = $provider;
	}

	private function assertValidRuleName(string $name)
	{
		if (in_array($name, Validator::getReservedRuleNames())) throw new \InvalidArgumentException('Rule name is reserved: ' . $name);
	}

	public function hasRule(string $name): bool
	{
		return isset($this->ruleClassMap[$name]);
	}

	public function resolve(string $name, array $data): RuleInterface
	{
		$provider = $this->getProviderForName($name);
		if ($provider !== null) return $provider->resolve($name, $data);
		// Check if we have a class mapping available
		if (!isset($this->ruleClassMap[$name])) {
			throw new \InvalidArgumentException('Rule not found: ' . $name);
		}

		return $this->resolveFromClassName($this->ruleClassMap[$name], $data);
	}

	private function getProviderForName(string $name): ?RuleProvider
	{
		foreach ($this->ruleProviders as $provider) {
			if ($provider->hasRule($name)) {
				return $provider;
			}
		}

		return null;
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