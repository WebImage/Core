<?php

namespace WebImage\Validation\Rules;

use WebImage\Validation\AbstractRule;

class ArrayRule extends AbstractRule
{
	protected string $message = '{label} must be an array';

	protected function doValidation($value): bool
	{
		return is_array($value);
	}
}