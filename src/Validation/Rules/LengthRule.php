<?php

namespace WebImage\Validation\Rules;

use WebImage\Core\ArrayHelper;
use WebImage\Validation\AbstractRule;
use WebImage\Validation\RuleFromArrayFactoryInterface;
use WebImage\Validation\RuleInterface;

class LengthRule extends AbstractRule implements RuleFromArrayFactoryInterface
{
	protected string $message = '{label} must be exactly {length} characters long';
	private int $length;

	public function __construct(int $length)
	{
		$this->length = $length;
	}

	public function validate($value): bool
	{
		if (!is_string($value)) return false;

		return strlen($value) === $this->length;
	}

	public static function fromArray(array $rule): RuleInterface
	{
		if (ArrayHelper::isAssociative($rule, true)) {
			ArrayHelper::assertKeys($rule, 'rule', ['length']);
			$min = ArrayHelper::get($rule, 'min');
		} else if (count($rule) === 0 || !is_numeric($rule[0])) {
			throw new \InvalidArgumentException('LengthRule expects a numeric parameter');
		} else {
			$min = $rule[0];
		}

		return new LengthMinRule($min);
	}

	public function toArray(): array
	{
		return array_merge(parent::toArray(), [
			'length' => $this->length
		]);
	}
}