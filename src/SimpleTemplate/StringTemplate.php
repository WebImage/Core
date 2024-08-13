<?php

namespace WebImage\SimpleTemplate;

class StringTemplate
{
	private string $template;
	private array  $data;

	public function __construct(string $template, array $data = [])
	{
		$this->template = $template;
		$this->data     = $data;
	}

	public function render(array $data = []): string
	{
		if (!preg_match_all('/{[a-zA-Z0-9_]+?}/', $this->template, $matches)) {
			return $this->template;
		}

		$merged = array_merge($this->data, $data);

		$rendered = $this->template;
		foreach($matches[0] as $var) {
			$key = substr($var, 1, -1);
			if (!array_key_exists($key, $merged)) {
				throw new \InvalidArgumentException('Missing data for key: ' . $key);
			}
			$rendered = str_replace($var, $merged[$key], $rendered);
		}

		return $rendered;
	}

	public static function renderString(string $template, array $data = []): string
	{
		return (new StringTemplate($template, $data))->render();
	}
}