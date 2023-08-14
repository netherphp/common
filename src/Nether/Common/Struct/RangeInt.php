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

		return ($Val >= $this->Min) && ($Val <= $this->Max);
	}

};
