<?php

namespace Nether\Common\Filesystem;

use FilterIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class RecursiveIterator
extends FilterIterator {

	public function
	__Construct(string $Dir) {

		$Flags = (
			0
			| RecursiveDirectoryIterator::SKIP_DOTS
			| RecursiveDirectoryIterator::CURRENT_AS_FILEINFO
		);

		parent::__Construct(
			new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator(
					$Dir, $Flags
				)
			)
		);

		return;
	}

	public function
	Accept():
	bool {

		return TRUE;
	}

}
