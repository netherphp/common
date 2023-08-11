<?php

namespace Nether\Common\Struct;

class RangeInt {

	public int
	$Min;

	public int
	$Max;

	public int
	$Diff;

	public function
	__Construct(int $Min, int $Max) {

		$this->Min = $Min;
		$this->Max = $Max;
		$this->Diff = $Max - $Min;

		return;
	}

	public function
	In(int $Val):
	bool {

		return (TRUE
			&& $Val >= $this->Min
			&& $Val <= $this->Max
		);
	}

	public function
	Diffcent(float $Val):
	float {

		$Out = 0.0;



		return $Out;
	}

};
