<?php

namespace WebImage\Validation\Rules;

use WebImage\Core\ArrayHelper;
use WebImage\Validation\AbstractRule;
use WebImage\Validation\RuleFromArrayFactoryInterface;
use WebImage\Validation\RuleInterface;

class ValidValueRule extends AbstractRule implements RuleFromArrayFactoryInterface
{
	protected string $message = '{label} is invalid';

	private array $validValues;

	public function __construct(array $validValues)
	{
		$this->validValues = $validValues;
	}

	protected function doValidation($value): bool
	{
		return in_array($value, $this->validValues);
	}

	public function toArray(): array
	{
		return array_merge(parent::toArray(), [
			'validValues' => $this->validValues
		]);
	}

	/**
	 * @param array $rule
	 * @return RuleInterface
	 */
	public static function fromArray(array $rule): RuleInterface
	{
		if (ArrayHelper::isAssociative($rule, true)) {
			ArrayHelper::assertKeys($rule, 'ValidValueRule', ['validValues']);
			return new static($rule['validValues']);
		} else if (count($rule) != 1) { // If rule is a simple array, then it should only have one value
			throw new \InvalidArgumentException('ValidValueRule expects a single value');
		} else {
			if (is_string($rule[0])) {
				$validValues = strlen($rule[0]) == 0 ? [] : explode(',', $rule[0]);
				return new static($validValues);
			} else if (is_array($rule[0])) {
				return new static($rule[0]);
			} else {
				throw new \InvalidArgumentException('ValidValueRule expects a string or array');
			}
		}
	}
}