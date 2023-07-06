<?php

namespace Nether\Common\Filters;

use Nether\Common;

class Numbers
extends Common\Datafilters {

	static public function
	IntFromNumeric(mixed $Val, int $Spaces=4):
	int {

		Common\Datafilters::Prepare($Val);

		if(is_int($Val))
		return $Val;

		if(is_string($Val))
		$Val = match(TRUE) {
			str_starts_with($Val, '0o'),
			=> octdec($Val),

			str_starts_with($Val, '0x')
			=> hexdec($Val),

			str_starts_with($Val, '0b')
			=> bindec($Val),

			default
			=> (int)$Val
		};

		return (int)$Val;
	}


}
