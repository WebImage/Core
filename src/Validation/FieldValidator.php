<?php

namespace WebImage\Validation;

use WebImage\Core\ArrayHelper;
use WebImage\Core\Dictionary;

class FieldValidator
{
	private string     $field;
	private array      $rules = [];
	private Dictionary $meta;

	/**
	 * @param string $field
	 * @param RuleInterface[] $rules
	 * @param Dictionary|null $meta
	 */
	public function __construct(string $field, array $rules, Dictionary $meta = null)
	{
		ArrayHelper::assertItemTypes($rules, RuleInterface::class);
		$this->field = $field;
		$this->rules = $rules;
		$this->meta  = $meta ?? new Dictionary();
	}

	/**
	 * @return string
	 */
	public function getField(): string
	{
		return $this->field;
	}

	/**
	 * @return array
	 */
	public function getRules(): array
	{
		return $this->rules;
	}

	public function getMeta(): Dictionary
	{
		return $this->meta;
	}
}