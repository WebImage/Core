<?php

namespace WebImage\Config;

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
	public function testGetTraversal()
	{
		$config = new Config([
								 'foo' => [
									 'bar' => [
										 'baz' => 'qux'
									 ]
								 ]
							 ]);

		$this->assertEquals('qux', $config->get('foo.bar.baz'));
	}

	public function testGetReturnsConfig()
	{
		$config = new Config([
								 'foo' => [
									 'bar' => [
										 'baz' => 'qux'
									 ]
								 ]
							 ]);

		$this->assertInstanceOf(Config::class, $config->get('foo.bar'));
	}

	public function testMultiStepTraversalConfig()
	{
		$config = new Config([
								 'foo' => [
									 'bar' => [
										 'baz' => 'qux'
									 ]
								 ]
							 ]);

		$firstTier = $config->get('foo.bar');

		$this->assertEquals('qux', $firstTier->get('baz'));
	}

	public function testCanTraversal()
	{
		$config = new Config([
								 'foo' => [
									 'bar' => [
										 'baz' => 'qux'
									 ]
								 ]
							 ]);

		$this->assertTrue($config->has('foo.bar.baz'));
	}

	public function testTraversalWithEscapedKeyGet()
	{
		$config = new Config([
								 'foo' => [
									 'bar.baz' => [
										 'value' => 'correct',
									 ],
									 'bar' => [
										 'baz' => [
											 'value' => 'incorrect'
										 ]
									 ]
								 ]
							 ]);

		$this->assertEquals('correct', $config->get('foo.[bar.baz].value'));
	}

	public function testTraversalWithIntermediateEscapedKey()
	{
		$config = new Config([
								 'foo' => [
									 'bar.baz' => [
										 'value' => 'correct',
									 ],
									 'bar' => [
										 'baz' => [
											 'value' => 'incorrect'
										 ]
									 ]
								 ]
							 ]);

		$this->assertEquals(new Config(['value' => 'correct']), $config->get('foo.[bar.baz]'));
	}

	public function testTraversalWithConfigValue()
	{
		$config = new Config([
								 'foo' => [
									 'bar' => [
										 'name' => 'baz'
									 ]
								 ]
							 ]);

		$this->assertTrue($config->has('foo.bar'));
	}
}