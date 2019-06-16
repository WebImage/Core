<?php

namespace WebImage\Core;

use League\Container\ReflectionContainer;

class VarDumper
{
	public static function toHtml($var)
	{
		return self::traverseVar($var);
	}

	private static function traverseVar($var)
	{
		$html = '';
		if (is_string($var)) $html .= self::renderString($var);
		else if (is_array($var)) {
			$html .= self::renderArray($var);
		} else if (is_object($var)) {
			$html .= self::renderObject($var);
		}

		return $html;
	}

	private static function renderString($str)
	{
		$quoteColor = '#090';
		$valColor = '#f09';

		return sprintf('<span style="color:%s">&quot;</span><span style="color:%s">%s</span><span style="color:%1$s;">&quot;</span>', $quoteColor, $valColor, $str);
	}

	private static function renderArray($var)
	{
		$html = '<ul>';

		foreach($var as $key => $val)
		{
			$html .= sprintf('<li>%s = %s</li>', self::renderVarName($key), self::traverseVar($val));
		}

		$html .= '</ul>';

		return $html;
	}

	private static function renderObject($var)
	{
		$class = new \ReflectionClass($var);

		$html = '';
		$html .= '#<span style="color: #999; font-style: italic;">' . $class->getName() . '</span><br>';

		$html .= '<ul>';

		foreach($class->getProperties() as $property) {
			$property->setAccessible(true);
			$html .= '<li>';
			if ($property->isPublic()) $html .= self::renderKeyword('public');
			else if ($property->isProtected()) $html .= self::renderKeyword('protected');
			else if ($property->isPrivate()) $html .= self::renderKeyword('private');

			if ($property->isStatic()) $html .= ' ' . self::renderKeyword('static');

			$html .= ' ' . self::renderVarName($property->getName()) . ' = ';

			$val = $property->getValue($var);
			if ($val instanceof \WebImage\Container\Container) {
				$html .= '<em>container</em>';
			} else {
				$html .= self::traverseVar($val);
			}

			$html .= '</li>';
		}

		$html .= '</ul>';

		return $html;
	}

	private static function renderVarName($name)
	{
		return '<span style="color: #09c;">' . $name . '</span>';
	}
	private static function renderKeyword($keyword)
	{
		return sprintf('<span style="color:#00c; font-weight: bold;">%s</span>', $keyword);
	}
}