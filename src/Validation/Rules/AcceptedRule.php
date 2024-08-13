<?php

namespace WebImage\Validation\Rules;

use WebImage\Validation\AbstractRule;
use WebImage\Validation\RuleValidationResult;

class AcceptedRule extends AbstractRule
{
	protected string $message = '{label} must be accepted';

	public function validate($value): bool
	{
		return $value == 'yes' || $value == 'on' || $value == 1 || $value == true;
	}
}