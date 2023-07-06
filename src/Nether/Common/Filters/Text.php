<?php

namespace Nether\Common\Filters;

use Nether\Common;

class Text
extends Common\Datafilters {

	static public function
	Tabbify(mixed $Val, int $Spaces=4):
	string {

		Common\Datafilters::Prepare($Val);

		$Val = preg_replace_callback(
			'#^ {1,}#ms',
			fn($Matches)
			=> str_repeat("\t", (int)(strlen($Matches[0]) / $Spaces)),
			$Val
		);

		return $Val;
	}


}
