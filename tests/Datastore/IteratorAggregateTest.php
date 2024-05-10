<?php

namespace NetherTestSuite\Common\Datastore;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Nether\Common;

use PHPUnit\Framework\TestCase;
use Exception;
use ArrayIterator;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class IteratorAggregateTest
extends TestCase {

	/** @test */
	public function
	TestGettingIterator():
	void {

		$Store = new Common\Datastore([ 1, 2, 3 ]);
		$Iter = $Store->GetIterator();
		$Key = NULL;
		$Val = NULL;

		$this->AssertInstanceOf(ArrayIterator::class, $Iter);
		$this->AssertEquals(3, $Iter->Count());

		foreach($Iter as $Key => $Val) {
			$this->AssertEquals($Store[$Key], $Val);
		}

		$Iter->Rewind();
		$Store->Push(4);

		// adding things after does not update this iterator.

		$this->AssertEquals(3, $Iter->Count());
		$this->AssertEquals(4, $Store->Count());


		return;
	}

};
