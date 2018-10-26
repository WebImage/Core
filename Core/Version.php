<?php

namespace WebImage\Core;

class Version {
	/** @var int */
	private $major;
	/** @var int */
	private $minor;
	/** @var int */
	private $revision;
	/** @var string */
	private $label;

	/**
	 * Version constructor.
	 *
	 * @param int $major
	 * @param int $minor
	 * @param int $revision
	 * @param string|null $label
	 */
	public function __construct($major, $minor, $revision, $label=null)
	{
		$this->major = $major;
		$this->minor = $minor;
		$this->revision = $revision;
		$this->label = $label;
	}

	/**
	 * @return int
	 */
	public function getMajor()
	{
		return $this->major;
	}

	/**
	 * @return int
	 */
	public function getMinor()
	{
		return $this->minor;
	}

	/**
	 * @return int
	 */
	public function getRevision()
	{
		return $this->revision;
	}

	/**
	 * @return string
	 */
	public function getLabel()
	{
		return $this->label;
	}

	/**
	 * Convert class to user friendly string
	 *
	 * @return string
	 */
	public function __toString()
	{
		$format = empty($this->label) ? '%s.%s.%s' : '%s.%s.%s %s';

		return sprintf($format, $this->getMajor(), $this->getMinor(), $this->getRevision(), $this->getLabel());
	}

	/**
	 * Create a Version from a string in the format [major].[minor].[revision] [label]
	 *
	 * @param $str
	 *
	 * @return Version
	 */
	public static function createFromString($str)
	{
		list($version_str, $label) = array_pad(explode(' ', $str, 2), 2, '');
		list($major, $minor, $revision) = array_pad(explode('.', $version_str, 3), 3, 0);

		return new static($major, $minor, $revision, $label);
	}
}