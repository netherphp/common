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
			[ [ -1, 'one', 'not' ], 'not' ],
			[ [  0, 'one', 'not' ], 'not' ],
			[ [  1, 'one', 'not' ], 'one' ],
			[ [  2, 'one', 'not' ], 'not' ]
		];

		////////

		$Input = NULL;

		foreach($Dataset as $Input)
		$this->AssertEquals(
			Values::IfOneElse(...$Input[0]),
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
			[ [ TRUE,  'yeh', 'nah' ], 'yeh' ],
			[ [ FALSE, 'yeh', 'nah' ], 'nah' ],
			[ [ 1,     'yeh', 'nah' ], 'yeh' ],
			[ [ 0,     'yeh', 'nah' ], 'nah' ],

			// things that typecast false.
			[ [ '0',   'yeh', 'nah' ], 'nah' ],
			[ [ '',    'yeh', 'nah' ], 'nah' ],

			// things that typecast true.
			[ [ '1',   'yeh', 'nah' ], 'yeh' ],
			[ [ 'wat', 'yeh', 'nah' ], 'yeh' ],
			[ [ '   ', 'yeh', 'nah' ], 'yeh' ],
			[ [ 'y',   'yeh', 'nah' ], 'yeh' ],
			[ [ 'n',   'yeh', 'nah' ], 'yeh' ]

		];

		////////

		$Input = NULL;

		foreach($Dataset as $Input)
		$this->AssertEquals(
			Values::IfTrueElse(...$Input[0]),
			$Input[1]
		);

		return;
	}

}