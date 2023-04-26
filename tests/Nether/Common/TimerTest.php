<?php

namespace Nether\Common;

use PHPUnit\Framework\TestCase;

use Throwable;

class TimerTest
extends TestCase {

	public function
	ThingToDo(float $Time=0.1):
	void {

		usleep($Time * 1000000);
		return;
	}

	public function
	OtherThingToDo(float $Time=0.0):
	void {

		$this->ThingToDo($Time);
		return;
	}

	/** @test */
	public function
	TestBasic():
	void {

		$Timer = new Timer($this->ThingToDo(...));

		// run a timer.

		$Timer->Run();
		$this->AssertGreaterThan(0.1, $Timer->Time);
		$this->AssertLessThan(0.2, $Timer->Time);

		// accumulate more time on the same timer.

		$Timer->Run();
		$this->AssertGreaterThan(0.2, $Timer->Time);
		$this->AssertLessThan(0.3, $Timer->Time);

		// reset the timer.

		$Timer->Reset();
		$this->AssertEquals(0.0, $Timer->Time);

		// run timer with a passed argument.

		$Timer->Run(0.2);
		$this->AssertGreaterThan(0.2, $Timer->Time);
		$this->AssertLessThan(0.3, $Timer->Time);

		// run timer via invoke and passed arg.

		$Timer->Reset();
		$Time = $Timer(NULL, 0.2);
		$this->AssertGreaterThan(0.2, $Time);
		$this->AssertLessThan(0.3, $Time);

		$Timer->Reset();
		$Time = $Timer($this->OtherThingToDo(...), 0.2);
		$this->AssertGreaterThan(0.2, $Time);
		$this->AssertLessThan(0.3, $Time);

		return;
	}

	/** @test */
	public function
	TestYouFailed():
	void {

		$Timer = new Timer;
		$Exceptional = FALSE;

		try {
			$Timer->Run(0);
		}

		catch(Throwable $Error) {
			$Exceptional = TRUE;

			$this->AssertInstanceOf(
				Error\MissingCallableFunc::class,
				$Error
			);
		}

		$this->AssertTrue($Exceptional);

		return;
	}

}