<?php

namespace WebImage\Validation;

use InvalidArgumentException;
use WebImage\Core\ArrayHelper;
use WebImage\Core\Dictionary;
use WebImage\Validation\Rules\RequiredRule;

class Validator
{
	private RuleResolver $ruleResolver;
	/** @var FieldValidator[] */
	private array $validators = [];

	/**
	 * @param RuleResolver|null $ruleResolver
	 */
	public function __construct(RuleResolver $ruleResolver = null)
	{
		$this->ruleResolver = $ruleResolver ?? RuleResolverFactory::create();
	}

	/**
	 * Validate an array of key-value pairs
	 * @param array $data
	 * @return FieldValidationError[]
	 */
	public function validate(array $data): array
	{
		$errors = [];

		foreach ($this->validators as $field => $validator) {
			foreach ($validator->getRules() as $rule) {
				$vars = array_merge(
					$rule->toArray(),
					$validator->getMeta()->toArray(),
					$rule instanceof ValidatorRuleInterface ? $rule->getValidatorMessageVars($this, $validator, $data) : []
				);

				if ($rule instanceof ValidatorRuleInterface) {
					if (!$rule->validateField($field, $data[$field] ?? null, $data)) {
						$errors[] = new FieldValidationError(
							$field,
							$rule->getMessage(),
							$vars
						);
						if ($rule->shouldHaltValidation()) break;
					}
				} else {
					if (!$rule->validate($data[$field] ?? null)) {
						$errors[] = new FieldValidationError(
							$field,
							$rule->getMessage(),
							$vars
						);
						if ($rule->shouldHaltValidation()) break;
					}
				}
			}
		}

		return $errors;
	}

	/**
	 * Create an instance of Validator from an array
	 * If 'meta' is defined, it will be passed to the FieldValidator as meta values
	 * @param array $validation
	 * @param RuleResolver|null $resolver
	 * @return Validator
	 */
	public static function fromArray(array $validation, RuleResolver $resolver = null): Validator
	{
		$validator = new Validator($resolver);

		foreach ($validation as $fieldName => $aRules) {
			if (!is_array($aRules)) $aRules = [$aRules];

			$meta = null;
			if (array_key_exists('meta', $aRules)) {
				$meta = new Dictionary(ArrayHelper::get($aRules, 'meta', []));
				unset($aRules['meta']);
			}
			$rules = $validator->rulesFromArray($aRules);
			$validator->addValidation($fieldName, $rules, $meta);
		}

		return $validator;
	}

	/**
	 * @param string $fieldName
	 * @param array $rules
	 * @param array $meta
	 * @return FieldValidator
	 */
	public function addValidation(string $fieldName, array $rules, Dictionary $meta = null): FieldValidator
	{
		ArrayHelper::assertItemTypes($rules, RuleInterface::class);
		$this->validators[$fieldName] = new FieldValidator($fieldName, $rules, $meta);

		return $this->validators[$fieldName];
	}

	/**
	 * @param array $aRules
	 * @return RuleInterface[]
	 */
	private function rulesFromArray(array $aRules): array
	{
		$rules = [];
		foreach ($aRules as $ix => $aRule) {
			$ruleName   = is_numeric($ix) ? $aRule : $ix;
			$ruleParams = is_numeric($ix) ? [] : (is_array($aRule) ? $aRule : [$aRule]);
			$rules[]    = $this->createRule($ruleName, $ruleParams);
		}

		return $rules;
	}

	public function createRule(string $ruleName, array $params = []): RuleInterface
	{
		return $this->ruleResolver->resolve($ruleName, $params);
	}

	public function hasField(string $fieldName): bool
	{
		return array_key_exists($fieldName, $this->validators);
	}

	public function getField(string $fieldName): FieldValidator
	{
		if (!$this->hasField($fieldName)) {
			throw new InvalidArgumentException('A field validator does not exist for ' . $fieldName);
		}

		return $this->validators[$fieldName];
	}

	public static function getReservedRuleNames(): array
	{
		return ['meta'];// Used by Validator::fromArray(...)
	}
}