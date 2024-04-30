<?php

use \WebImage\Core\Collection;
use WebImage\Core\Dictionary;

class ArrayHelperTest extends \PHPUnit\Framework\TestCase
{
	public function testMerge()
	{
		$merged = \WebImage\Core\ArrayHelper::merge(
			['a' => 1, 'b' => 2],
			['b' => 3, 'c' => 4]
		);

		$this->assertEquals(['a' => 1, 'b' => 3, 'c' => 4], $merged);
	}

	public function testFirst()
	{
		$first = 'Value #1';
		$second = 'Value #2';
		$third = 'Value #3';

		$array = [$first, $second, $third];

		$found = \WebImage\Core\ArrayHelper::first($array, function($val, $key) use ($second) {
			return $val === $second;
		});

		$this->assertEquals($second, $found);
	}

	public function testIsAssociative()
	{
		$this->assertTrue(\WebImage\Core\ArrayHelper::isAssociative(['a' => 1, 'b' => 2]));
		$this->assertFalse(\WebImage\Core\ArrayHelper::isAssociative([1, 2, 3]));
	}

	public function testIsAssociativeTrueForEmptyArray()
	{
		$this->assertTrue(\WebImage\Core\ArrayHelper::isAssociative([]));
	}

	public function testAssertKeysRequiredKeys()
	{
		$this->expectException(\InvalidArgumentException::class);
		\WebImage\Core\ArrayHelper::assertKeys(['a' => 1, 'b' => 2], 'root', ['a', 'b', 'c']);
	}

	public function testAssertKeysMissingOptionalKeys()
	{
		$this->expectNotToPerformAssertions();
		\WebImage\Core\ArrayHelper::assertKeys(['a' => 1, 'b' => 2], 'root', ['a', 'b'], ['c']);
	}

	public function testAssertKeysOptionalKeysAllowed()
	{
		$this->expectNotToPerformAssertions();
		\WebImage\Core\ArrayHelper::assertKeys(['a' => 1, 'b' => 2, 'c' => 3], 'root', ['a', 'b'], ['c']);
	}

	public function testAssertItemTypesString()
	{
		$this->expectNotToPerformAssertions();
		\WebImage\Core\ArrayHelper::assertItemTypes(['a', 'b', 'c'], 'string');
	}

	public function testAssertItemTypesInvalidString()
	{
		$this->expectException(\InvalidArgumentException::class);
		\WebImage\Core\ArrayHelper::assertItemTypes(['a', 'b', 3], 'string');
	}

	public function testAssertItemTypesInt()
	{
		$this->expectNotToPerformAssertions();
		\WebImage\Core\ArrayHelper::assertItemTypes([1, 2, 3], 'integer');
	}

	public function testAssertItemTypesInvalidInt()
	{
		$this->expectException(\InvalidArgumentException::class);
		\WebImage\Core\ArrayHelper::assertItemTypes(['a', 'b', 3], 'integer');
	}

	public function testAssertItemTypesDouble()
	{
		$this->expectNotToPerformAssertions();
		\WebImage\Core\ArrayHelper::assertItemTypes([1.0, 2.0, 3.0], 'double');
	}

	public function testAssertItemTypesInvalidDouble()
	{
		$this->expectException(\InvalidArgumentException::class);
		\WebImage\Core\ArrayHelper::assertItemTypes(['a', 'b', 3], 'double');
	}

	public function testAssertItemTypesClass()
	{
		$this->expectNotToPerformAssertions(\InvalidArgumentException::class);
		\WebImage\Core\ArrayHelper::assertItemTypes([
			new Dictionary(),
				new Dictionary(),
				new Dictionary()
			], Dictionary::class
		);
	}
}