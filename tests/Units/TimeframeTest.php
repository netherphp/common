<?php

namespace NetherTestSuite\Common;

use Nether\Common\Values;
use Nether\Common\Units\Timeframe;
use PHPUnit\Framework\TestCase;

class TimeframeTest
extends TestCase {

	/** @test */
	public function
	TestBasic():
	void {

		$FormatSets = [
			'FormatDefault' => [
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

		$Time = new Timeframe;
		$this->RunFormattedTests($Time, $FormatSets);

		return;
	}

	/** @test */
	public function
	TestZeros():
	void {

		$FormatSets = [
			'FormatDefault' => [
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

		$Time = new Timeframe;
		$Time->SetSkipZero(FALSE);
		$this->RunFormattedTests($Time, $FormatSets);

		return;
	}

	/** @test */
	public function
	TestInvokeStyle():
	void {

		$Time = new Timeframe;

		$this->AssertEquals('5sec', $Time(5, 10));
		$this->AssertEquals('1min 1sec', $Time(0, 61));

		return;
	}

	/** @test */
	public function
	TestJoin():
	void {

		$Time = new Timeframe(0, 61);

		$this->AssertEquals('1min 1sec', $Time->SetJoin(' ')->Get());
		$this->AssertEquals('1min, 1sec', $Time->SetJoin(', ')->Get());
		$this->AssertEquals('1min-1sec', $Time->SetJoin('-')->Get());

		$this->AssertEquals('1min 1sec', $Time->Get(Join: ' '));
		$this->AssertEquals('1min, 1sec', $Time->Get(Join: ', '));
		$this->AssertEquals('1min-1sec', $Time->Get(Join: '-'));

		return;
	}

	/** @test */
	public function
	TestFormat():
	void {

		$Time = new Timeframe(0, 61);

		$this->AssertEquals('1m 1s', $Time->SetFormat($Time::FormatShort)->Get());
		$this->AssertEquals('1 minute 1 second', $Time->SetFormat($Time::FormatLong)->Get());

		return;
	}

	/** @test */
	public function
	TestStringable():
	void {

		$Time = new Timeframe(0, 61);

		$this->AssertEquals('1min 1sec', (string)$Time);
		$this->AssertEquals('1hr', "{$Time->Span(0, 3600)}");

		return;
	}

	/** @test */
	public function
	TestCurrentOffsets():
	void {

		$Time = new Timeframe;

		$this->AssertEquals('', $Time());
		$this->AssertEquals('- 5d', $Time(Start: '+5 days'));
		$this->AssertEquals('5d', $Time(Stop: '+5 days'));

		return;
	}

	/** @test */
	public function
	TestPrecision():
	void {

		$Time = new Timeframe(Stop: '+1 year 1 month 1 day 1 hour 1 min 1 sec');

		$this->AssertEquals('1yr 1mo 1d 1hr 1min 1sec', $Time->Get());
		$this->AssertEquals('1yr 1mo', $Time->SetPrecision(2)->Get());
		$this->AssertEquals('1yr 1mo 1d', $Time->Get(Precision: 3));
		$this->AssertEquals('1yr 1mo 1d 1hr', $Time(Precision: 4) );

		return;
	}

	/** @test */
	public function
	TestEmptyString():
	void {

		$Time = new Timeframe(Start: 0, Stop: 0);

		$this->AssertEquals('', $Time->Get());
		$this->AssertEquals('OK', $Time->Get(EmptyString: 'OK'));

		$Time->SetEmptyString('YO');
		$this->AssertEquals('YO', $Time->Get());
		$this->AssertEquals('JO', $Time->Get(EmptyString: 'JO'));

		////////

		$Time = new Timeframe(Start: 0, Stop: 0, EmptyString: 'KO');
		$this->AssertEquals('KO', $Time->Get());
		$this->AssertEquals('BO', $Time->Get(EmptyString: 'BO'));

		$Time->SetEmptyDiff(60);
		$this->AssertEquals('KO', $Time->Get());

		$Time->SetStop(60);
		$this->AssertEquals('KO', $Time->Get());

		$Time->SetStop(61);
		$this->AssertEquals('1min 1sec', $Time->Get());

		return;
	}

	/** @test */
	public function
	TestTimeDiff():
	void {

		$Time = new Timeframe(1, 10);

		$this->AssertEquals(9, $Time->GetTimeDiff());

		return;
	}

	/** @test */
	public function
	TestDecadeInner():
	void {

		$Time = Timeframe::FromDecadeInner(1980);

		$this->AssertEquals($Time->GetStartFormat('Y-m-d H:i:s'), '1980-01-01 00:00:00');
		$this->AssertEquals($Time->GetStopFormat('Y-m-d H:i:s'), '1989-12-31 23:59:59');
		$this->AssertEquals(315532800, $Time->GetStartTime());
		$this->AssertEquals(631151999, $Time->GetStopTime());
		$this->AssertTrue(str_starts_with($Time->Get(), '9yr 11mo '));

		return;
	}

	/** @test */
	public function
	TestDecadeOuter():
	void {

		$Time = Timeframe::FromDecadeOuter(1980);

		$this->AssertEquals($Time->GetStartFormat('Y-m-d H:i:s'), '1980-01-01 00:00:00');
		$this->AssertEquals($Time->GetStopFormat('Y-m-d H:i:s'), '1990-01-01 00:00:00');
		$this->AssertEquals(315532800, $Time->GetStartTime());
		$this->AssertEquals(631152000, $Time->GetStopTime());
		$this->AssertEquals($Time->Get(), '10yr');

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected function
	RunFormattedTests(Timeframe $Time, array $FormatSets):
	void {

		$Format = NULL;
		$Sets = NULL;
		$Expected = NULL;
		$Set = NULL;

		foreach($FormatSets as $Format => $Sets)
		foreach($Sets as $Expected => $Set) {
			$Time->Span($Set[0], $Set[1]);

			$this->AssertEquals(
				$Expected,
				$Time->Get(constant(sprintf(
					'%s::%s',
					Timeframe::class,
					$Format
				))),
				json_encode($Set)
			);
		}

		return;
	}

}