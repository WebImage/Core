<?php

namespace WebImage\TypeResolver;

trait KeyResolvedTypeTrait
{
	/**
	 * @var string
	 */
	private $_resolvedTypeKey;
	/**
	 * A key used to resolve an instantiated type (from TypeResolver)
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function setResolvedTypeKey(string $key)
	{
		$this->_resolvedTypeKey = $key;
	}

	/**
	 * The key used to resolve an instantiated type (from TypeResolver)
	 * @return null|string
	 */
	public function getResolvedTypeKey(): ?string
	{
		return $this->_resolvedTypeKey;
	}
}