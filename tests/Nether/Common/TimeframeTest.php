<?php

namespace Nether\Common;

use PHPUnit\Framework\TestCase;

class TimeframeTest
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

		$Time = new Units\Timeframe;
		$this->RunFormattedTests($Time, $FormatSets);

		return;
	}

	/** @test */
	public function
	TestZeros():
	void {

		$FormatSets = [
			'FormatNormal' => [
				'0yr 0mo 0d 0hr 0min 0sec'
				=> [ 1234, 1234 ],

				'0yr 0mo 0d 0hr 0min 1sec'
				=> [ 1234, 1235 ],

				'0yr 0mo 0d 0hr 1min 0sec'
				=> [ 0, Values::SecPerMin ],

				'0yr 0mo 0d 1hr 0min 0sec'
				=> [ 0, Values::SecPerHr ],

				'0yr 0mo 1d 0hr 0min 0sec'
				=> [ 0, Values::SecPerDay ],

				'0yr 1mo 0d 0hr 0min 0sec'
				=> [ 'Feb 1 2023', 'Mar 1 2023' ],

				'0yr 1mo 0d 0hr 0min 0sec'
				=> [ 'Mar 1 2023', 'Apr 1 2023' ],

				'0yr 1mo 0d 0hr 0min 0sec'
				=> [ 'Feb 1 2024', 'Mar 1 2024' ],

				'0yr 1mo 0d 0hr 0min 0sec'
				=> [ 'Mar 1 2024', 'Apr 1 2024' ],

				'1yr 0mo 0d 0hr 0min 0sec'
				=> [ 'Jan 1 2023', 'Jan 1 2024' ],

				'1yr 0mo 0d 0hr 0min 0sec'
				=> [ 'Jan 1 2024', 'Jan 1 2025' ],

				'- 1yr 0mo 0d 0hr 0min 0sec'
				=> [ 'Jan 1 2002', 'Jan 1 2001' ]
			],
			'FormatLong' => [
				'0 years 0 months 1 day 0 hours 0 minutes 0 seconds'
				=> [ 0, Values::SecPerDay ],

				'0 years 0 months 2 days 0 hours 0 minutes 0 seconds'
				=> [ 0, (Values::SecPerDay * 2) ]
			]
		];

		$Time = new Units\Timeframe;
		$Time->SetSkipZero(FALSE);
		$this->RunFormattedTests($Time, $FormatSets);

		return;
	}

	protected function
	RunFormattedTests(Units\Timeframe $Time, array $FormatSets):
	void {

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

		return;
	}

}