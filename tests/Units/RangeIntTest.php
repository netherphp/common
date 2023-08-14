<?php

namespace NetherTestSuite\Common;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Nether\Common;

use PHPUnit\Framework\TestCase;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class RangeIntTest
extends TestCase {

	/** @test */
	public function
	TestBasic():
	void {

		$Range = new Common\Struct\RangeInt(1, 10);

		$this->AssertEquals(1, $Range->Min);
		$this->AssertEquals(10, $Range->Max);
		$this->AssertEquals(9, $Range->Diff);

		$this->AssertFalse($Range->In(-1));
		$this->AssertFalse($Range->In(0));
		$this->AssertTrue($Range->In(1));
		$this->AssertTrue($Range->In(5));
		$this->AssertTrue($Range->In(10));
		$this->AssertFalse($Range->In(11));

		return;
	}

}