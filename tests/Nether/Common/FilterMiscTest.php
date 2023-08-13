<?php

namespace Nether\Common;

use Exception;
use PHPUnit\Framework\TestCase;
use Nether\Common\Filters\Misc;

class FilterMiscTest
extends TestCase {

	/** @test */
	public function
	TestNullable():
	void {

		$this->AssertNull(Misc::Nullable(0));
		$this->AssertNull(Misc::Nullable(''));
		$this->AssertNull(Misc::Nullable(FALSE));
		$this->AssertNull(Misc::Nullable([]));

		return;
	}

}