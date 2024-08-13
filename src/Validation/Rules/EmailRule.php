<?php

namespace WebImage\Validation\Rules;

use WebImage\Validation\AbstractRule;

class EmailRule extends AbstractRule
{
	protected string $message = '{label} must be a valid email address';

	public function validate($value): bool
	{
		return is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL);
	}
}