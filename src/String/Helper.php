<?php

namespace WebImage\String;

class Helper {
	/**
	 * @var $str
	 */
//	private $str;
//
	/**
	 * StringBuilder constructor.
	 * @param $str
	 */
//	public function __construct($str)
//	{
//		$this->str = $str;
//	}

	public static function startsWith($str, $with)
	{
		return (static::sub($str, $len = static::len($with), $len) == $with);
	}

	public static function endsWith($str, $with)
	{
		return (static::sub($str, $len = -static::len($with), $len) == $with);
	}

	public static function sub($str, $start, $len=null)
	{
		return substr($str, $start, $len);
	}

	public static function len($str)
	{
		return strlen($str);
	}

	public static function pascalToHyphenated($pascalName)
	{
		self::keysFromPascalCase($pascalName);

		return implode('-', self::keysFromPascalCase($pascalName));
	}

	public static function pascalToUnderscore(string $pascalName)
	{
		return implode('-', self::keysFromPascalCase($pascalName));
	}

	public static function camelToHyphenated(string $camelName)
	{
		return implode('-', self::keysFromPascalCase($camelName));
	}

	public static function camelToUnderscore(string $camelName)
	{
		return implode('_', self::keysFromPascalCase($camelName));
	}

	private static function keysFromPascalCase($pascalName)
	{
		$escaped = preg_replace('/[^a-zA-Z0-9]+/', ' ', $pascalName);
		$spaced = trim(preg_replace('/[A-Z]+/', ' \0', $escaped));

		return preg_split('/ +/', $spaced);
	}
}
