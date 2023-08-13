<?php

namespace Nether\Common\Package;

use Nether\Common;

// the current intent is that this will be common until the root Datafilter
// class is purged out of all the old methods. then this should go away and
// all the filter classes should extend that root one again.

#[Common\Meta\DateAdded('2023-08-13')]
trait DatafilterPackage {

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

}
