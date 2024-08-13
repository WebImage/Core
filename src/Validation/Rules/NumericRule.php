<?php

namespace WebImage\Validation\Rules;

use WebImage\Validation\AbstractRule;

class NumericRule extends AbstractRule
{
	protected string $message = '{label} must be numeric';

	public function validate($value): bool
	{
		return is_numeric($value);
	}
}