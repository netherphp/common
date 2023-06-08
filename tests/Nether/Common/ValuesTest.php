<?php

namespace Nether\Common;

use PHPUnit\Framework\TestCase;

class ValuesTest
extends TestCase {

	/** @test */
	public function
	TestDebugProtectValue():
	void {

		$Out = Values::DebugProtectValue('asdf');
		$this->AssertEquals('[protected string len:4]', $Out);

		$Out = Values::DebugProtectValue(69);
		$this->AssertEquals('[protected integer]', $Out);

		return;
	}


}