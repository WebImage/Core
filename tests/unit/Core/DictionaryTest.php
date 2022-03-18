<?php

use PHPUnit\Framework\TestCase;
use WebImage\Core\Dictionary;

class DictionaryTest extends TestCase
{
	public function testIntegerValue(): void
	{
		$d = new Dictionary();
		$key = 'key';

		$intVal = 100;
		$d->set($key, $intVal);

		$this->assertIsInt($d->get($key), 'Return value must be an int');
		$this->assertEquals($intVal, $d->get($key), 'Setting integer value must return same integer value');
	}

	public function testCount(): void
	{
		$d = new Dictionary();

		$d->set('first', 'Robert');
		$d->set('last', 'Jones');

		$this->assertCount(2, $d, 'Correct count must be stored when setting items');
	}

	public function testArrayConstructor(): void
	{
		$d = new Dictionary(['first' => 'Robert', 'last' => 'Jones']);

		$this->assertEquals('Robert', $d['first']);
	}

	public function testReturnNullWhenNoValueSet(): void
	{
		$d = new Dictionary();

		$this->assertNull($d['non-existent']);
	}

	public function testUnset(): void
	{
		$d = new Dictionary(['unset' => 'unset']);
		unset($d['unset']);

		$this->assertNull($d['unset']);
	}
}