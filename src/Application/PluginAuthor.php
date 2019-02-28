<?php

namespace WebImage\Application;

class PluginAuthor {
	/** @var string The name of the plugin author */
	private $name;
	/** @var string The author's email address */
	private $email;
	/** @var string The author's email */
	private $company;

	/**
	 * PluginAuthor constructor.
	 *
	 * @param string $name
	 * @param string $email
	 * @param string $company
	 */
	public function __construct($name, $email, $company)
	{
		$this->name = $name;
		$this->email = $email;
		$this->company = $company;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * @return string
	 */
	public function getCompany()
	{
		return $this->company;
	}
}