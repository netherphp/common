<?php

namespace Nether\Common\Filters;

use Nether\Common;

class Misc
extends Common\Datafilters {

	static public function
	Nullable(mixed $Item):
	mixed {
	/*//
	if the input evals to FALSE, its now a NULL.
	//*/

		static::Prepare($Item);

		return $Item ?: NULL;
	}

}
