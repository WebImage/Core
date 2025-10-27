<?php

namespace WebImage\Config;

class ConfigArrayModifier
{
	private string $filePath;
	private array $config;
	private string $content;

	public function __construct(string $filePath)
	{
		$this->filePath = $filePath;
		if (!file_exists($filePath)) {
			throw new \InvalidArgumentException("Config file does not exist: {$filePath}");
		}

		$this->content = file_get_contents($filePath);
		$this->config = require $filePath;

		if (!is_array($this->config)) {
			throw new \InvalidArgumentException("Config file must return an array");
		}
	}

	public function get(string $path, $default = null, bool $dotNotation = true)
	{
		$keys = $dotNotation ? explode('.', $path) : [$path];
		$current = $this->config;

		foreach ($keys as $key) {
			if (!is_array($current) || !array_key_exists($key, $current)) {
				return $default;
			}
			$current = $current[$key];
		}

		return $current;
	}

	public function set(string $path, $value, bool $dotNotation = true): self
	{
		$keys = $dotNotation ? explode('.', $path) : [$path];
		$this->updateArray($this->config, $keys, $value);
		$this->updateContent($keys, $value, 'set');
		return $this;
	}

	public function add(string $path, $value, bool $dotNotation = true): self
	{
		$keys = $dotNotation ? explode('.', $path) : [$path];
		$current = &$this->config;
		foreach ($keys as $key) {
			if (!isset($current[$key])) $current[$key] = [];
			$current = &$current[$key];
		}
		$current[] = $value;
		$this->updateContent($keys, $value, 'add');
		return $this;
	}

	public function del(string $path, bool $dotNotation = true): self
	{
		$keys = $dotNotation ? explode('.', $path) : [$path];
		$this->deleteFromArray($this->config, $keys);
		$this->updateContent($keys, null, 'del');
		return $this;
	}

	public function addEmptyArray(string $path, bool $dotNotation = true): self
	{
		return $this->set($path, [], $dotNotation);
	}

	public function save(): bool
	{
		return file_put_contents($this->filePath, $this->content) !== false;
	}

	public function getConfig(): array
	{
		return $this->config;
	}

	private function updateArray(array &$array, array $keys, $value): void
	{
		$current = &$array;
		foreach (array_slice($keys, 0, -1) as $key) {
			if (!isset($current[$key]) || !is_array($current[$key])) {
				$current[$key] = [];
			}
			$current = &$current[$key];
		}
		$current[end($keys)] = $value;
	}

	private function deleteFromArray(array &$array, array $keys): void
	{
		$current = &$array;
		foreach (array_slice($keys, 0, -1) as $key) {
			if (!isset($current[$key])) return;
			$current = &$current[$key];
		}
		unset($current[end($keys)]);
	}

	private function updateContent(array $keys, $value, string $operation): void
	{
		$pattern = $this->buildPattern($keys);

		if ($operation === 'del') {
			$this->content = preg_replace($pattern, '', $this->content);
		} else {
			$replacement = $this->formatValue($keys, $value, $operation);
			if (preg_match($pattern, $this->content)) {
				$this->content = preg_replace($pattern, $replacement, $this->content);
			} else {
				$this->insertNewValue($keys, $value);
			}
		}
	}

	private function buildPattern(array $keys): string
	{
		$keyPath = implode("'\\s*\\]\\s*\\[\\s*'", array_map('preg_quote', $keys));
		return "/^(\s*)(['\"]?" . preg_quote($keys[0]) . "['\"]?.*?=>.*?(?:\[.*?\].*?)*?)$/m";
	}

	private function formatValue(array $keys, $value, string $operation): string
	{
		$key = end($keys);
		$indent = $this->detectIndentation();

		if ($operation === 'add') {
			return var_export($value, true);
		}

		$formattedValue = $this->formatPhpValue($value);
		return "'{$key}' => {$formattedValue},";
	}

	private function insertNewValue(array $keys, $value): void
	{
		$key = end($keys);
		$formattedValue = $this->formatPhpValue($value);
		$indent = $this->detectIndentation();
		$newLine = "{$indent}'{$key}' => {$formattedValue},\n";

		// Find the last array element and insert before the closing bracket
		$pos = strrpos($this->content, ']');
		if ($pos !== false) {
			$this->content = substr_replace($this->content, $newLine . substr($this->content, $pos), $pos, 0);
		}
	}

	private function formatPhpValue($value): string
	{
		if (is_array($value)) {
			if (empty($value)) return '[]';
			$items = array_map(fn($v) => $this->formatPhpValue($v), $value);
			return '[' . implode(', ', $items) . ']';
		}
		return var_export($value, true);
	}

	private function detectIndentation(): string
	{
		preg_match('/^(\s+)/m', $this->content, $matches);
		return $matches[1] ?? '    ';
	}
}