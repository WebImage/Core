<?php

namespace WebImage\TypeResolver;

interface KeyResolvedTypeInterface
{
	/**
	 * A key used to resolve an instantiated type (from TypeResolver)
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function setResolvedTypeKey(string $key);

	/**
	 * The key used to resolve an instantiated type (from TypeResolver)
	 * @return null|string
	 */
	public function getResolvedTypeKey(): ?string;
}