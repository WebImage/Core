<?php

use \WebImage\Core\Collection;

class CollectionTest extends \PHPUnit\Framework\TestCase
{
	public function testCollectionAdd()
	{
		$c = new Collection();
		$c[] = 1;
		$c[] = 2;

		$this->assertArrayHasKey(1, $c, 'Adding record should result in new index existing');
		$this->assertEquals(2, count($c), 'Count should return the number of elements added');
	}
	
	public function testCollectionDelete()
	{
		$c = new Collection();
		$c[] = 1;
		$c[] = 2;
		$c[] = 3;

		$c->del(1);
		$this->assertArrayNotHasKey(1, $c, 'Collection key should be missing once deleted');
	}
	
	public function testCollectionUnset()
	{
		$c = new Collection();
		$c[] = 1;
		$c[] = 2;
		$c[] = 3;

		unset($c[1]);
		$this->assertArrayNotHasKey(1, $c, 'Deleting collection index using unset should work');
	}

	public function testConstructorNonNumericValues()
	{
		$this->expectException(InvalidArgumentException::class);
		$c = new Collection([1, 2, 'name' => 'Batman']);
	}

	public function testToArray()
	{
		$expected = [1,3,5,7,9,8,6,4,2,0];
		$c = new Collection($expected);
		$this->assertEquals($expected, $c->toArray());
	}

	public function testInsert()
	{
		$c = new Collection([1,3,4]);
		$c->insert(1, 2);
		
		$this->assertEquals([1,2,3,4], $c->toArray());
	}
	
	public function testMapping()
	{
		$starting = new Collection([1,2,4]);
		$expected = [1,4,16];
		$power = 2;

		$c = $starting->map(function($val) use ($power) {
			return pow($val, $power);
		});

		$this->assertEquals($expected, $c->toArray(), 'Mapping should work');
		$this->assertNotEquals($c->toArray(), $starting->toArray(), 'Mapping should not affect the original values');
	}
}