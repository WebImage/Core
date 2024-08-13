<?php

namespace WebImage\Validation\Rules;

use WebImage\Validation\AbstractRule;

class BooleanRule extends AbstractRule
{
	protected string $message = '{label} must be a boolean';

	public function validate($value): bool
	{
		return is_bool($value);
	}
}