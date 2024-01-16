<?php

namespace WebImage\Core;

use League\Container\ReflectionContainer;
use ReflectionClass;

class VarDumper
{
	public static function toText($var, array $excludeClasses = []): string
	{
		$structure = self::structure($var, $excludeClasses);

		return self::renderVar($structure);
	}

	private static function renderVar(array $var, int $depth = 0): string
	{
		switch ($var['type']) {
			case 'object':
				$text = '';
				if (!$var['exclude']) {
					$text .= self::renderVarTypeAndValue($var, $depth);
					$text .= self::renderVarChildren($var, $depth + 1);
				}
				return $text;
			case 'array':
				$text = self::renderVarTypeAndValue($var, $depth);
				$text .= self::renderVarChildren($var, $depth + 1);
				return $text;
			default:
				return self::renderVarTypeAndValue($var, $depth);
		}
	}

	private static function renderVarTypeAndValue(array $var, int $depth, string $prefix = ''): string
	{
		if ($var['type'] == 'object' && $var['exclude']) return '';

		$type = $var['type'] == 'object' ? $var['class'] : $var['type'];
		if ($type == 'array') {
			$type .= '-' . (ArrayHelper::isAssociative($var['array']) ? 'associative':'indexed');
			$type .= '(' . count($var['array']) . ')';
		}
		if ($var['type'] != 'NULL') $type = '(' . $type . ')';

		$text = $prefix;
		$text .= $type;

		$value = self::renderVarValue($var);
		if (strlen($value)) $value = ' ' . $value;
		$text .= $value;

		return self::renderLine($text, $depth);
	}

	private static function renderVarChildren(array $var, int $depth): string
	{
		switch ($var['type']) {
			case 'object':
				$text = '';
				if (!$var['exclude'] && !$var['recursive']) {
					foreach ($var['properties'] as $property => $value) {
						$text .= self::renderVarTypeAndValue($value, $depth, '[' . $property . '] ');
						$text .= self::renderVarChildren($value, $depth + 1);
					}
				}
				return $text;
			case 'array';
				$text = '';
				$isAssociative = ArrayHelper::isAssociative($var['array']);
				foreach ($var['array'] as $key => $value) {
					$prefix = '- ';
					if ($isAssociative) $prefix .= $key . ' => ';
					else $prefix .= $key . ') ';
					$text .= self::renderVarTypeAndValue($value, $depth, $prefix);
					$text .= self::renderVarChildren($value, $depth + 1);
				}
				return $text;
			default:
				return '';
		}
	}

	private static function renderVarValue(array $var): string
	{
		switch ($var['type']) {
			case 'float':
			case 'int':
			case 'string':
			case 'bool':
				return $var['value'];
			case 'NULL':
			case 'array':
			case 'object':
				return '';
			default:
				return 'OTHER:' . $var['type'];
		}
	}

	private static function renderLine(string $text, int $depth = 0)
	{
		return str_repeat(' ', 4 * $depth) . $text . PHP_EOL;
	}

	public static function structure($var, array $excludeClasses = [])
	{
		return self::traverseVar($var, $excludeClasses);
	}

	private static function traverseVar($var, array $excludeClasses, array $parents = []): array
	{
		$data = [
			'type' => 'unknown'
		];

		if ($var === null) {
			$data['type'] = 'NULL';
		} else if (is_string($var)) {
			$data['type']  = 'string';
			$data['value'] = $var;
		} else if (is_float($var)) {
			$data['type']  = 'float';
			$data['value'] = $var;
		} else if (is_int($var)) {
			$data['type']  = 'int';
			$data['value'] = $var;
		} else if (is_bool($var)) {
			$data['type']  = 'bool';
			$data['value'] = $var ? 'true' : 'false';
		} else if (is_array($var)) {
			$data['type']  = 'array';
			$data['array'] = array_map(function ($val) use ($excludeClasses, $parents) {
				return self::traverseVar($val, $excludeClasses, $parents);
			}, $var);
		} else if (is_object($var)) {
			$data['type']       = 'object';
			$data['class']      = get_class($var);
			$data['properties'] = [];
			$data['exclude']    = false;
			$data['recursive']  = false;

			$exclude = false;
			foreach ($excludeClasses as $excludeClass) {
				if ($var instanceof $excludeClass) {
					$exclude = true;
					break;
				}
			}
			$recursive = false;
			foreach ($parents as $parent) {
				if ($var === $parent) {
					$recursive = true;
					break;
				}
			}

			if ($exclude) {
				$data['exclude'] = true;
			} else if ($recursive) {
				$data['recursive'] = true;
			} else {
				$data['properties'] = self::traverseProperties($var, $excludeClasses, $parents);
			}
		}

		return $data;
	}

	private static function traverseProperties($var, array $excludeClasses, array $parents)
	{
		$properties          = [];
		$class               = new ReflectionClass($var);
		$reflectedProperties = $class->getProperties();
		$current             = $class;
		while ($parent = $current->getParentClass()) {
			$reflectedProperties = array_merge($reflectedProperties, $parent->getProperties());
			$current             = $current->getParentClass();
		}

		foreach ($reflectedProperties as $property) {
			$property->setAccessible(true);

			$properties[$property->getName()] = self::traverseVar($property->getValue($var), $excludeClasses, array_merge($parents, [$var]));
//			if ($property->isPublic()) $html .= self::renderKeyword('public');
//			else if ($property->isProtected()) $html .= self::renderKeyword('protected');
//			else if ($property->isPrivate()) $html .= self::renderKeyword('private');
//
//			if ($property->isStatic()) $html .= ' ' . self::renderKeyword('static');

//			$html .= ' ' . self::renderVarName($property->getName()) . ' = ';

//			$val = $property->getValue($var);
		}


		return $properties;
	}

	public static function toHtml($var)
	{
		return self::traverseVarForHtml($var);
	}

	private static function traverseVarForHtml($var)
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
		$valColor   = '#f09';

		return sprintf('<span style="color:%s">&quot;</span><span style="color:%s">%s</span><span style="color:%1$s;">&quot;</span>', $quoteColor, $valColor, $str);
	}

	private static function renderArray($var)
	{
		$html = '<ul>';

		foreach ($var as $key => $val) {
			$html .= sprintf('<li>%s = %s</li>', self::renderVarName($key), self::traverseVarForHtml($val));
		}

		$html .= '</ul>';

		return $html;
	}

	private static function renderObject($var)
	{
		$class = new ReflectionClass($var);

		$html = '';
		$html .= '#<span style="color: #999; font-style: italic;">' . $class->getName() . '</span><br>';

		$html .= '<ul>';

		foreach ($class->getProperties() as $property) {
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
				$html .= self::traverseVarForHtml($val);
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
