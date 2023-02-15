<?php

namespace Nether\Common;

use PHPUnit\Framework\TestCase;

class UnitsBytesTest
extends TestCase {

	/** @test */
	public function
	TestBasic():
	void {

		$Bytes = new Units\Bytes(69);

		$this->AssertEquals('69 b', $Bytes->Get());

		return;
	}

}