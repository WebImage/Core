<?php

namespace WebImage\Validation\Rules;

use WebImage\Core\ArrayHelper;
use WebImage\Validation\AbstractRule;
use WebImage\Validation\RuleFromArrayFactoryInterface;
use WebImage\Validation\RuleInterface;

class EqualsRule extends AbstractRule implements RuleFromArrayFactoryInterface
{
	protected string $message = '{label} does not match {equalsValue}';

	/** @var mixed */
	private $equalsValue;

	public function __construct($equalsValue)
	{
		$this->equalsValue = $equalsValue;
	}

	protected function doValidation($value): bool
	{
		return $value === $this->equalsValue;
	}

	public static function fromArray(array $rule): RuleInterface
	{
		if (ArrayHelper::isAssociative($rule, true)) {
			ArrayHelper::assertKeys($rule, 'EqualsRule', ['equalsValue']);
			return new static($rule['equalsValue']);
		} else {
			if (count($rule) !== 1) {
				throw new \InvalidArgumentException('EqualsRule expects a single value');
			}
			return new static($rule[0]);
		}
	}

	public function toArray(): array
	{
		return array_merge(parent::toArray(), [
			'equalsValue' => $this->equalsValue
		]);
	}
}