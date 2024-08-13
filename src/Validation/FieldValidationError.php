<?php

namespace WebImage\Validation;

use WebImage\SimpleTemplate\StringTemplate;

class FieldValidationError
{
	private string $field;
	private string $message;
	private array  $data;

	public function __construct(string $field, string $message = '', array $data = [])
	{
		$this->field   = $field;
		$this->message = $message;
		$this->data    = $data;
	}

	public function getField(): string
	{
		return $this->field;
	}

	public function getMessage(): string
	{
		return $this->message;
	}

	public function getData(): array
	{
		return $this->data;
	}

	public function render(): string
	{
		return StringTemplate::renderString($this->message, $this->data);
	}

	public function __toString()
	{
		return $this->render();
	}
}