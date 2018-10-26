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
		echo 'STR: ' . $str . '<br />';
		echo 'COMPARE: (' . static::sub($str, $len = static::len($with), $len) . ' == ' . $with . ')<br />';
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
}