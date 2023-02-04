<?php

namespace Nether\Common;

use PHPUnit\Framework\TestCase;

class DatafilterTest
extends TestCase {

	/** @test */
	public function
	TestBasic():
	void {

		$FormatSets = [
			'FormatNormal' => [
				''       => [ 1234, 1234 ],
				'1sec'   => [ 1234, 1235 ],
				'1min'   => [ 0, Values::SecPerMin ],
				'1hr'    => [ 0, Values::SecPerHr ],
				'1d'     => [ 0, Values::SecPerDay ],
				'1mo'    => [ 'Feb 1 2023', 'Mar 1 2023' ],
				'1mo'    => [ 'Mar 1 2023', 'Apr 1 2023' ],
				'1mo'    => [ 'Feb 1 2024', 'Mar 1 2024' ],
				'1mo'    => [ 'Mar 1 2024', 'Apr 1 2024' ],
				'1yr'    => [ 'Jan 1 2023', 'Jan 1 2024' ],
				'1yr'    => [ 'Jan 1 2024', 'Jan 1 2025' ],
				'- 1yr'  => [ 'Jan 1 2002', 'Jan 1 2001' ]
			],
			'FormatLong' => [
				'1 day'  => [ 0, Values::SecPerDay ],
				'2 days' => [ 0, (Values::SecPerDay * 2) ]
			]
		];

		////////

		$Time = new Units\Timeframe;
		$Format = NULL;
		$Sets = NULL;
		$Expected = NULL;
		$Set = NULL;

		foreach($FormatSets as $Format => $Sets)
		foreach($Sets as $Expected => $Set) {
			$Time->Set($Set[0], $Set[1]);

			$this->AssertEquals(
				$Expected,
				$Time->Get(constant(sprintf(
					'%s::%s',
					Units\Timeframe::class,
					$Format
				)))
			);
		}


		$this->AssertTrue(TRUE);

		return;
	}

}