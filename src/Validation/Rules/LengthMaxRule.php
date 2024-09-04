<?php

namespace WebImage\Validation\Rules;

use WebImage\Core\ArrayHelper;
use WebImage\Validation\AbstractRule;
use WebImage\Validation\RuleFromArrayFactoryInterface;
use WebImage\Validation\RuleInterface;

class LengthMaxRule extends AbstractRule implements RuleFromArrayFactoryInterface
{
	protected string $message = '{label} must be less than {max} characters long';
	private int $max;

	/**
	 * @param int $max
	 */
	public function __construct(int $max)
	{
		$this->max = $max;
	}

	protected function doValidation($value): bool
	{
		return is_string($value) && strlen($value) <= $this->max;
	}

	public static function fromArray(array $rule): RuleInterface
	{
		if (ArrayHelper::isAssociative($rule, true)) {
			ArrayHelper::assertKeys($rule, 'rule', ['max']);
			$min = ArrayHelper::get($rule, 'max');
		} else if (count($rule) === 0 || !is_numeric($rule[0])) {
			throw new \InvalidArgumentException('LengthMaxRule expects a numeric parameter');
		} else {
			$min = $rule[0];
		}

		return new LengthMaxRule($min);
	}

	public function toArray(): array
	{
		return array_merge(parent::toArray(), [
			'max' => $this->max
		]);
	}
}