<?php

namespace WebImage\Validation\Rules;

use WebImage\Core\ArrayHelper;
use WebImage\Validation\AbstractRule;
use WebImage\Validation\RuleFromArrayFactoryInterface;
use WebImage\Validation\RuleInterface;

class MaxRule extends AbstractRule implements RuleFromArrayFactoryInterface
{
	protected string $message = '{label} must be less than {min}';
	private int $min;

	/**
	 * @param int $max
	 */
	public function __construct(int $max)
	{
		$this->min = $max;
	}

	protected function doValidation($value): bool
	{
		return is_numeric($value) && $value <= $this->min;
	}

	public static function fromArray(array $rule): RuleInterface
	{
		if (ArrayHelper::isAssociative($rule, true)) {
			ArrayHelper::assertKeys($rule, 'rule', ['max']);
			$max = ArrayHelper::get($rule, 'max');
		} else if (count($rule) === 0 || !is_numeric($rule[0])) {
			throw new \InvalidArgumentException('MaxRule expects a numeric parameter');
		} else {
			$max = $rule[0];
		}

		return new MaxRule($max);
	}

	public function toArray(): array
	{
		return array_merge(parent::toArray(), [
			'max' => $this->min
		]);
	}
}