<?php

namespace WebImage\Validation;

interface RuleFromArrayFactoryInterface
{
	public static function fromArray(array $rule): RuleInterface;
}