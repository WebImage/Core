<?php

namespace WebImage\Validation\Rules;

use WebImage\Validation\AbstractRule;

class BooleanRule extends AbstractRule
{
	protected string $message = '{label} must be a boolean';

	protected function doValidation($value): bool
	{
		return is_bool($value);
	}
}