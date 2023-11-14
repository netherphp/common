<?php

namespace Nether\Common;

class Math {

	static public function
	Clamp(int|float $Val, int|float $Min, int|float $Max):
	int|float {

		return max(min($Max, $Val), $Min);
	}

};
