<?php

namespace WebImage\Validation\Rules;

use WebImage\Validation\AbstractRule;

class DecimalNumberRule extends AbstractRule
{
	protected string $message = '{label} is not a valid decimal number';
	protected function doValidation($value): bool
	{
		return (is_string($value) && preg_match('/^[0-9]+(\.[0-9]+)?$/', $value)) || is_float($value) || is_double($value) || is_int($value);
	}
}