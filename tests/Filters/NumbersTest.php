<?php

namespace NetherTestSuite\Common\Filters;

use Exception;
use PHPUnit\Framework\TestCase;
use Nether\Common\Filters\Numbers;

class NumbersTest
extends TestCase {

	/** @test */
	public function
	TestIntFromNumeric():
	void {

		$Data = [ 42, '42', '0o52', '0x2A', '0b00101010' ];
		$Num = NULL;

		foreach($Data as $Num)
		$this->AssertEquals(42, Numbers::IntFromNumeric($Num));

		return;
	}

}