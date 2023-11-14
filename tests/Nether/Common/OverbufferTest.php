<?php

namespace Nether\Common;

use PHPUnit\Framework\TestCase;

class OverbufferTest
extends TestCase {

	/** @test */
	public function
	TestBasic():
	void {

		$Buf = new Overbuffer;
		$Derper = (fn()=> printf('derp%s', PHP_EOL));
		$Loop = 0;

		// empty.

		$this->AssertEquals('', $Buf->Get());
		$this->AssertTrue($Buf->GetKeep());

		// fill it up.

		for($Loop = 1; $Loop <= 3; $Loop++) {
			$Buf->Execute($Derper);

			$this->AssertEquals(
				str_repeat(sprintf('derp%s', PHP_EOL), $Loop),
				$Buf->Get()
			);

			$this->AssertEquals(
				$Buf->Length(),
				strlen(str_repeat(sprintf('derp%s', PHP_EOL), $Loop))
			);
		}

		// empty it again.

		$Buf->Clear();
		$this->AssertEquals('', $Buf->Get());

		// fill it again.

		for($Loop = 1; $Loop <= 3; $Loop++) {
			$Buf->Execute($Derper);

			$this->AssertEquals(
				str_repeat(sprintf('derp%s', PHP_EOL), $Loop),
				$Buf->Get()
			);

			$this->AssertEquals(
				$Buf->Length(),
				strlen(str_repeat(sprintf('derp%s', PHP_EOL), $Loop))
			);
		}

		// empty it again.

		$Buf->Clear();
		$Buf->SetKeep(FALSE);
		$this->AssertEquals('', $Buf->Get());
		$this->AssertFalse($Buf->GetKeep());

		// but now its only ever the last line.

		for($Loop = 1; $Loop <= 3; $Loop++) {
			$Buf->Execute($Derper);

			$this->AssertEquals(
				str_repeat(sprintf('derp%s', PHP_EOL), 1),
				$Buf->Get()
			);

			$this->AssertEquals(
				$Buf->Length(),
				strlen(str_repeat(sprintf('derp%s', PHP_EOL), 1))
			);
		}

		return;
	}


}