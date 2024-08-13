<?php

namespace WebImage\Validation;

use WebImage\Core\Dictionary;

interface ValidatorRuleInterface
{
	public function validateField(string $field, $value, array $data): bool;
	public function getValidatorMessageVars(Validator $validator, FieldValidator $fieldValidator, array $data): array;
}