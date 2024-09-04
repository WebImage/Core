<?php

namespace WebImage\Validation\Rules;

use WebImage\Validation\AbstractRule;

class NumericRule extends AbstractRule
{
	protected string $message = '{label} must be numeric';

	protected function doValidation($value): bool
	{
		return is_numeric($value);
	}
}