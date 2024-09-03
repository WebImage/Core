<?php

namespace WebImage\Validation;

interface RuleFactoryInterface
{
	public function createRule(array $data, string $rule): RuleInterface;
}