<?php

namespace Nether\Common;

class Timer {

	public float
	$Time = 0.0;

	////////

	protected float
	$Start = 0.0;

	protected float
	$Stop = 0.0;

	protected mixed
	$Callable = NULL;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(?callable $Thing=NULL) {

		$this->Callable = $Thing;

		return;
	}

	public function
	__Invoke(callable $Thing, ...$Argv):
	float {

		$this->Set($Thing);
		$this->Run(...$Argv);

		return $this->Time;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Run(...$Argv):
	static {

		if(!is_callable($this->Callable))
		throw new Error\MissingCallableFunc;

		////////

		$this->Start = microtime(TRUE);

		($this->Callable)(...$Argv);

		$this->Stop = microtime(TRUE);
		$this->Time += $this->Stop - $this->Start;

		return $this;
	}

	public function
	Reset():
	static {

		$this->Time = 0.0;
		$this->Start = 0.0;
		$this->Stop = 0.0;

		return $this;
	}

	public function
	Set(?callable $Thing=NULL):
	static {

		$this->Callable = $Thing;

		return $this;
	}

}
