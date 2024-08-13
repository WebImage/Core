<?php

namespace WebImage\Validation\Rules;

use WebImage\Validation\AbstractRule;

class IntegerRule extends AbstractRule
{
	protected string $message = '{label} must be an integer';

	public function validate($value): bool
	{
		return is_integer($value) ||
			   (is_string($value) && preg_match('^[0-9]+$', $value));
	}
}