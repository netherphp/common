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

	/** @test */
	public function
	TestIfOneElse():
	void {

		$Dataset = [
			[ -1, 'not' ],
			[  0, 'not' ],
			[  1, 'one' ],
			[  2, 'not' ]
		];

		////////

		$Input = NULL;

		foreach($Dataset as $Input)
		$this->AssertEquals(
			Values::IfOneElse($Input[0], 'one', 'not'),
			$Input[1]
		);

		return;
	}

	/** @test */
	public function
	TestIfTrueElse():
	void {

		$Dataset = [

			// plausable inputs.
			[ TRUE,  'yeh' ],
			[ FALSE, 'nah' ],
			[ 1,     'yeh' ],
			[ 0,     'nah' ],

			// things that typecast false.
			[ '0', 'nah' ],
			[ '',  'nah' ],

			// things that typecast true.
			[ '1',   'yeh' ],
			[ 'wat', 'yeh' ],
			[ '   ', 'yeh' ],
			[ 'y',   'yeh' ],
			[ 'n',   'yeh' ]

		];

		////////

		$Input = NULL;

		foreach($Dataset as $Input)
		$this->AssertEquals(
			Values::IfTrueElse($Input[0], 'yeh', 'nah'),
			$Input[1]
		);

		return;
	}

	/** @test */
	public function
	TestIsNumericDec():
	void {

		$Dataset = [
			[ 0,           TRUE ],
			[ '0',         TRUE ],
			[ '1',         TRUE ],
			[ '69',        TRUE ],
			[ 'sixtynine', FALSE ],
			[ '!@#$%^&*',  FALSE ],
			[ '10a10a',    FALSE ]
		];

		////////

		$Input = NULL;

		foreach($Dataset as $Input)
		$this->AssertEquals(
			Values::IsNumericDec($Input[0]),
			$Input[1]
		);

		return;
	}

	/** @test */
	public function
	TestIsNumericHex():
	void {

		$Dataset = [
			[ 0,           TRUE ],
			[ '0',         TRUE ],
			[ '1',         TRUE ],
			[ '69',        TRUE ],
			[ 'sixtynine', FALSE ],
			[ '!@#$%^&*',  FALSE ],
			[ '10a10a',    TRUE ]
		];

		////////

		$Input = NULL;

		foreach($Dataset as $Input)
		$this->AssertEquals(
			Values::IsNumericHex($Input[0]),
			$Input[1]
		);

		return;
	}

}