<?php

namespace WebImage\Validation;

class CoreRuleProvider extends RuleProvider
{
	protected function initRules(): void
	{
		$this->addRule('accepted', Rules\AcceptedRule::class);
		$this->addRule('array', Rules\ArrayRule::class);
		$this->addRule('boolean', Rules\BooleanRule::class);
		$this->addRule('email', Rules\EmailRule::class);
		$this->addRule('equals', Rules\EqualsRule::class);
		$this->addRule('equalsField', Rules\EqualsFieldValueRule::class);
		$this->addRule('integer', Rules\IntegerRule::class);
		$this->addRule('decimal', Rules\DecimalNumberRule::class);
		$this->addRule('length', Rules\LengthRule::class);
		$this->addRule('minLength', Rules\LengthMinRule::class);
		$this->addRule('maxLength', Rules\LengthMaxRule::class);
		$this->addRule('min', Rules\MinRule::class);
		$this->addRule('max', Rules\MaxRule::class);
		$this->addRule('numeric', Rules\NumericRule::class);
		$this->addRule('required', Rules\RequiredRule::class);
		$this->addRule('validValue', Rules\ValidValueRule::class);
//		'requiredWith', // Field is required if any other fields are present
//		'requiredWithout', // Field is required if any other fields are NOT present
//		'different', // Field must be different than another field
//		'lengthBetween', // String must be between given lengths
//		'listContains', // Performs in_array check on given array values (the other way round than in)
//		'in', // Performs in_array check on given array values
//		'notIn', // Negation of in rule (not in array of values)
//		'ip', // Valid IP address
//		'ipv4', // Valid IP v4 address
//		'ipv6', // Valid IP v6 address
//		'emailDNS', // Valid email address with active DNS record
//		'url', // Valid URL
//		'urlActive', // Valid URL with active DNS record
//		'alpha', // Alphabetic characters only
//		'alphaNum', // Alphabetic and numeric characters only
//		'ascii', // ASCII characters only
//		'slug', // URL slug characters (a-z, 0-9, -, _)
//		'regex', // Field matches given regex pattern
//		'date', // Field is a valid date
//		'dateFormat', // Field is a valid date in the given format
//		'dateBefore', // Field is a valid date and is before the given date
//		'dateAfter', // Field is a valid date and is after the given date
//		'contains', // Field is a string and contains the given string
//		'subset', // Field is an array or a scalar and all elements are contained in the given array
//		'containsUnique', // Field is an array and contains unique values
//		'creditCard', // Field is a valid credit card number
//		'instanceOf', // Field contains an instance of the given class
//		'optional', // Value does not need to be included in data array. If it is however, it must pass validation.
//		'arrayHasKeys', // Field is an array and contains all specified keys.
	}
}