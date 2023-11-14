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

		if($Min > $Max) {
			$Tmp = $Min;
			$Min = $Max;
			$Max = $Tmp;
			unset($Tmp);
		}

		$this->Min = $Min;
		$this->Max = $Max;
		$this->Diff = (int)($Max - $Min);

		return;
	}

	public function
	In(int $Val):
	bool {

		return ($Val >= $this->Min) && ($Val <= $this->Max);
	}

};
