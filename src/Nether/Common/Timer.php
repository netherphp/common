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
	__Invoke(?callable $Thing, ...$Argv):
	float {

		if(is_callable($Thing))
		$this->Set($Thing);

		$this->Run(...$Argv);

		return $this->Time;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Start():
	void {

		$this->Start = microtime(TRUE);

		return;
	}

	public function
	Stop():
	void {

		$this->Stop = microtime(TRUE);

		$this->Time += $this->Stop - $this->Start;

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Run(...$Argv):
	static {

		if(!is_callable($this->Callable))
		throw new Error\MissingCallableFunc;

		////////

		$this->Start();

		($this->Callable)(...$Argv);

		$this->Stop();

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

	public function
	Get():
	float {

		return $this->Time;
	}

}
