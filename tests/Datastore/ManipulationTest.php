<?php

namespace NetherTestSuite\Common\Datastore;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Nether\Common;

use PHPUnit\Framework\TestCase;
use Exception;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class ManipulationTest
extends TestCase {

	/** @test */
	public function
	TestHeadMethodsAgainstLists():
	void {

		$Data = [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ];
		$Val = NULL;

		$Store = Common\Datastore::FromArray($Data);
		$this->AssertEquals(10, $Store->Count());

		// [ 1, 2, 3, 4, 5 ]

		$Store->HeadCrop(5);
		$this->AssertEquals(5, $Store->Count());
		$this->AssertEquals(1, $Store[0]);
		$this->AssertEquals(5, $Store[4]);

		// [ 42, 1, 2, 3, 4, 5 ]

		$Store->HeadPush(42);
		$this->AssertEquals(6, $Store->Count());
		$this->AssertEquals(42, $Store[0]);
		$this->AssertEquals(1, $Store[1]);

		// [ 1, 2, 3, 4, 5 ] -> 42

		$Val = $Store->HeadPop();
		$this->AssertEquals(42, $Val);
		$this->AssertEquals(5, $Store->Count());
		$this->AssertEquals(1, $Store[0]);

		return;
	}

	/** @test */
	public function
	TestTailMethodsAgainstLists():
	void {

		$Data = [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ];
		$Val = NULL;

		$Store = Common\Datastore::FromArray($Data);
		$this->AssertEquals(10, $Store->Count());

		// [ 6, 7, 8, 9, 10 ]

		$Store->TailCrop(5);
		$this->AssertEquals(5, $Store->Count());
		$this->AssertEquals(6, $Store[0]);
		$this->AssertEquals(10, $Store[4]);

		// [ 6, 7, 8, 9, 10, 42 ]

		$Store->TailPush(42);
		$this->AssertEquals(6, $Store->Count());
		$this->AssertEquals(10, $Store[4]);
		$this->AssertEquals(42, $Store[5]);

		// [ 6, 7, 8, 9, 10 ] -> 42

		$Val = $Store->TailPop();
		$this->AssertEquals(5, $Store->Count());
		$this->AssertEquals(42, $Val);
		$this->AssertEquals(10, $Store[4]);
		$this->AssertNull($Store[5]);

		return;
	}

};
