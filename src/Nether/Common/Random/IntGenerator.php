<?php

namespace Nether\Common\Random;

use Random;
use Nether\Common;

class IntGenerator {

	public Random\Randomizer
	$API;

	public int
	$Min = PHP_INT_MIN;

	public int
	$Max = PHP_INT_MAX;

	public function
	__Construct(int $Seed=NULL) {

		if($Seed !== NULL)
		$this->Seed($Seed);

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Seed(?int $Seed=NULL) {

		if($Seed === NULL)
		$Seed = random_int(PHP_INT_MIN, PHP_INT_MAX);

		////////

		$this->API = new Random\Randomizer(new Random\Engine\Mt19937(
			$Seed, MT_RAND_MT19937
		));

		return;
	}

	public function
	Next(int $Min=NULL, int $Max=NULL):
	int {

		if(!isset($this->API))
		throw new Common\Error\RandomGeneratorNotSeeded;

		$Min ??= $this->Min;
		$Max ??= $this->Max;

		////////

		return $this->API->GetInt($Min, $Max);
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	SetRange(int $Min=NULL, int $Max=NULL):
	static {

		if($Min !== NULL)
		$this->Min = $Min;

		if($Max !== NULL)
		$this->Max = $Max;

		return $this;
	}

	public function
	SetRangeFor(iterable $List):
	static {

		$this->SetRange(0, count($List));

		return $this;
	}

};
