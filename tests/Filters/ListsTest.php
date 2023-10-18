<?php

namespace NetherTestSuite\Common\Filters;

use Nether\Common\Datastore;
use Nether\Common\Filters\Lists;
use Nether\Common\Filters\Numbers;
use Exception;
use PHPUnit\Framework\TestCase;

class ListsTest
extends TestCase {

	/** @test */
	public function
	TestFiltersArrayOf():
	void {

		$Array = Lists::ArrayOf(NULL);
		$this->AssertTrue(is_array($Array));
		$this->AssertEquals(1, count($Array));
		$this->AssertEquals(NULL, $Array[0]);

		$Array = Lists::ArrayOf('');
		$this->AssertTrue(is_array($Array));
		$this->AssertEquals(1, count($Array));
		$this->AssertEquals('', $Array[0]);

		$Array = Lists::ArrayOf(0);
		$this->AssertTrue(is_array($Array));
		$this->AssertEquals(1, count($Array));
		$this->AssertEquals(0, $Array[0]);

		$Array = Lists::ArrayOf('42');
		$this->AssertTrue(is_array($Array));
		$this->AssertEquals(1, count($Array));
		$this->AssertEquals('42', $Array[0]);

		////////

		$Array = Lists::ArrayOf(['42']);
		$this->AssertTrue(is_array($Array));
		$this->AssertEquals(1, count($Array));
		$this->AssertIsString($Array[0]);

		$Array = Lists::ArrayOf(['42'], Numbers::IntType(...));
		$this->AssertTrue(is_array($Array));
		$this->AssertEquals(1, count($Array));
		$this->AssertIsInt($Array[0]);

		$Array = Lists::ArrayOf(Datastore::FromArray(['42']));
		$this->AssertTrue(is_array($Array));
		$this->AssertEquals(1, count($Array));
		$this->AssertIsString($Array[0]);

		return;
	}

	/** @test */
	public function
	TestFiltersArrayOfNullable():
	void {

		// falsy things end up null.

		$Array = Lists::ArrayOfNullable(NULL);
		$this->AssertNull($Array);

		$Array = Lists::ArrayOfNullable('');
		$this->AssertNull($Array);

		$Array = Lists::ArrayOfNullable(0);
		$this->AssertNull($Array);

		$Array = Lists::ArrayOfNullable([]);
		$this->AssertNull($Array);

		$Array = Lists::ArrayOfNullable(Datastore::FromArray([]));
		$this->AssertNull($Array);

		////////

		$Array = Lists::ArrayOfNullable('42');
		$this->AssertTrue(is_array($Array));
		$this->AssertEquals(1, count($Array));
		$this->AssertEquals('42', $Array[0]);

		////////

		$Array = Lists::ArrayOfNullable(['42']);
		$this->AssertTrue(is_array($Array));
		$this->AssertEquals(1, count($Array));
		$this->AssertIsString($Array[0]);

		$Array = Lists::ArrayOfNullable(['42'], Numbers::IntType(...));
		$this->AssertTrue(is_array($Array));
		$this->AssertEquals(1, count($Array));
		$this->AssertIsInt($Array[0]);

		$Array = Lists::ArrayOfNullable(Datastore::FromArray(['42']));
		$this->AssertTrue(is_array($Array));
		$this->AssertEquals(1, count($Array));
		$this->AssertIsString($Array[0]);

		return;
	}

}