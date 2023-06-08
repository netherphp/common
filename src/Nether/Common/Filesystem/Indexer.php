<?php

namespace Nether\Common\Filesystem;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

use IteratorIterator;
use FilesystemIterator;

use FilterIterator;

class Indexer
extends FilterIterator {

	public function
	__Construct(string $Dir, bool $Recur=FALSE) {

		$Flags = (
			0
			| FilesystemIterator::SKIP_DOTS
			| FilesystemIterator::CURRENT_AS_FILEINFO
		);

		////////

		if($Recur)
		$Outermost = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($Dir, $Flags)
		);

		else
		$Outermost = new IteratorIterator(
			new FilesystemIterator($Dir, $Flags)
		);

		////////

		parent::__Construct($Outermost);
		return;
	}

	public function
	Accept():
	bool {

		return TRUE;
	}

}
