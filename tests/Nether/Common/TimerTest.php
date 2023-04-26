<?php

namespace Nether\Common;

use PHPUnit\Framework\TestCase;

class TimerTest
extends TestCase {

	public function
	ThingToDo(float $Time=1.1):
	void {

		usleep($Time * 1000000);
		return;
	}

	/** @test */
	public function
	TestBasic():
	void {

		$Timer = new Timer($this->ThingToDo(...));

		// run a timer.
		$Timer->Run();
		$this->AssertGreaterThan(1.0, $Timer->Time);

		// accumulate more time on the same timer.
		$Timer->Run();
		$this->AssertGreaterThan(2.0, $Timer->Time);

		// reset the timer.
		$Timer->Reset();
		$this->AssertEquals(0.0, $Timer->Time);

		// run timer with a passed argument.
		$Timer->Run(0.2);
		$this->AssertGreaterThan(0.2, $Timer->Time);
		$this->AssertLessThan(1.0, $Timer->Time);

		// run timer via invoke and passed arg.
		$Timer->Reset();
		$Time = $Timer(NULL, 0.3);
		$this->AssertGreaterThan(0.3, $Time);
		$this->AssertLessThan(1.0, $Time);

		return;
	}


}