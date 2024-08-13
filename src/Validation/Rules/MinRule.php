<?php

namespace WebImage\Validation\Rules;

use WebImage\Core\ArrayHelper;
use WebImage\Validation\AbstractRule;
use WebImage\Validation\RuleFromArrayFactoryInterface;
use WebImage\Validation\RuleInterface;

class MinRule extends AbstractRule implements RuleFromArrayFactoryInterface
{
	protected string $message = '{label} must be at least {min}';
	private int $min;

	/**
	 * @param int $min
	 */
	public function __construct(int $min)
	{
		$this->min = $min;
	}

	public function validate($value): bool
	{
		return is_numeric($value) && $value >= $this->min;
	}

	public static function fromArray(array $rule): RuleInterface
	{
		if (ArrayHelper::isAssociative($rule, true)) {
			ArrayHelper::assertKeys($rule, 'rule', ['min']);
			$min = ArrayHelper::get($rule, 'min');
		} else if (count($rule) === 0 || !is_numeric($rule[0])) {
			throw new \InvalidArgumentException('MinRule expects a numeric parameter');
		} else {
			$min = $rule[0];
		}

		return new MinRule($min);
	}

	public function toArray(): array
	{
		return array_merge(parent::toArray(), [
			'min' => $this->min
		]);
	}
}