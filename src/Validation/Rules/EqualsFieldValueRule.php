<?php

namespace WebImage\Validation\Rules;

use RuntimeException;
use SebastianBergmann\Diff\InvalidArgumentException;
use WebImage\Core\ArrayHelper;
use WebImage\Core\Dictionary;
use WebImage\Validation\AbstractRule;
use WebImage\Validation\FieldValidator;
use WebImage\Validation\RuleFromArrayFactoryInterface;
use WebImage\Validation\RuleInterface;
use WebImage\Validation\Validator;
use WebImage\Validation\ValidatorRuleInterface;

class EqualsFieldValueRule extends AbstractRule implements ValidatorRuleInterface, RuleFromArrayFactoryInterface
{
	protected string $message = '{label} must be equal to {otherFieldLabel}';
	private string  $otherField;
	private ?string $otherFieldLabel;

	/**
	 * @param string $otherField
	 */
	public function __construct(string $otherField)
	{
		$this->otherField      = $otherField;
		$this->otherFieldLabel = null;
	}

	public function getOtherField(): string
	{
		return $this->otherField;
	}

	public function setOtherField(string $otherField): void
	{
		$this->otherField = $otherField;
	}

	public function getOtherFieldLabel(): ?string
	{
		return $this->otherFieldLabel;
	}

	public function setOtherFieldLabel(string $otherFieldLabel): void
	{
		$this->otherFieldLabel = $otherFieldLabel;
	}

	protected function doValidation($value): bool
	{
		throw new RuntimeException(sprintf('%s should only be called from within Validator', __METHOD__));
	}

	public function validateField(string $field, $value, array $data): bool
	{
		$value2 = ArrayHelper::get($data, $this->otherField);
		return $value == $value2;
	}

	public function getValidatorMessageVars(Validator $validator, FieldValidator $fieldValidator, array $data): array
	{
		return [
			'otherFieldLabel' => $this->_getOtherFieldLabel($validator, $fieldValidator)
		];
	}

	private function _getOtherFieldLabel(Validator $validator, FieldValidator $fieldValidator): string
	{
		$otherField = $this->getOtherField();
		$label = $otherField;

		if ($fieldValidator->getMeta()->has('otherFieldLabel')) {
			$label = $fieldValidator->getMeta()->get('otherFieldLabel');
		} else if ($this->getOtherFieldLabel() !== null) { // Prioritize locally set label, if set
			$label = $this->getOtherFieldLabel();
		} else if ($validator->hasField($otherField) === true) {
			$otherField = $validator->getField($otherField);
			if ($otherField->getMeta()->has('label')) {
				$label = $otherField->getMeta()->get('label');
			}
		}

		return $label;
	}

	public static function fromArray(array $rule): RuleInterface
	{
		$otherFieldLabel = null;
		if (ArrayHelper::isAssociative($rule, true)) {
			ArrayHelper::assertKeys($rule, 'EqualsFieldValueRule', ['otherField', 'otherFieldLabel']);
			$otherField = ArrayHelper::get($rule, 'otherField');
			$otherFieldLabel = ArrayHelper::get($rule, 'otherFieldLabel');
		} else if (count($rule) != 1) {
			throw new \InvalidArgumentException('EqualsFieldValueRule expects a single parameter');
		} else {
			$otherField = $rule[0];
		}

		$rule = new EqualsFieldValueRule($otherField, $otherFieldLabel);
		if ($otherFieldLabel !== null) $rule->setOtherFieldLabel($otherFieldLabel);
		return $rule;
	}

	public function toArray(): array
	{
		return array_merge(parent::toArray(), [
			'otherField' => $this->getOtherField(),
			'otherFieldLabel' => $this->getOtherFieldLabel()
		]);
	}
}