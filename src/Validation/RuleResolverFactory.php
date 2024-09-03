<?php

namespace WebImage\Validation;

class RuleResolverFactory
{
	public static function create(): RuleResolver
	{
		$resolver = new RuleResolver();
		$resolver->addRuleProvider(new CoreRuleProvider());

		return $resolver;
	}
}