<?php

namespace NetherTestSuite\Common;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Nether\Common;

use PHPUnit\Framework\TestCase;
use Exception;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

// this is one of those stupid things to just spin up these exception
// instances to run and "test" them. i've started adding exceptions to this
// library that this library may not be using but all the others do so without
// this foolery they might never get run.

class ExceptionTest
extends TestCase {

	/** @test */
	public function
	TestBasic():
	void {

		$Data = [
			Common\Error\CrontabFormatInvalid::class
			=> [ 'omg' ],

			Common\Error\RequiredDataMissing::class
			=> [ 'Thingy', 'string' ]
		];

		$Class = NULL;
		$Argv = NULL;

		foreach($Data as $Class => $Argv) {
			$Err = new $Class(...$Argv);

			try { throw $Err; }
			catch(Exception $Ex) {
				$this->AssertInstanceOf($Class, $Ex);
			}
		}

		return;
	}

}