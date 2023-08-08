<?php

namespace Nether\Common\Filters;

use Nether\Common;

class Misc {

	#[Common\Meta\DateAdded('2023-08-07')]
	static public function
	Prepare(mixed &$Item):
	mixed {

		// @todo 2023-08-07 rebase class off Datafilters after all the old
		// methods are removed, then remove this method.

		if($Item instanceof Common\Struct\DatafilterItem)
		$Item = $Item->Value;

		return $Item;
	}

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
