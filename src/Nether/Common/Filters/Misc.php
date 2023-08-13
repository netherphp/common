<?php

namespace Nether\Common\Filters;

use Nether\Common;

#[Common\Meta\DateAdded('2023-07-07')]
class Misc {

	use
	Common\Package\DatafilterPackage;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

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
