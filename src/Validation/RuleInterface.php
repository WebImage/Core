<?php

namespace WebImage\Validation;

interface RuleInterface
{
	public function validate($value): bool;
	public function shouldHaltValidation(): bool;
	public function setHaltValidation(bool $haltValidation): void;
	public function getMessage(): string;
	public function setMessage(string $message): void;
	public function toArray(): array;
}