<?php

namespace Nether\Common\Error;

use Exception;

class DirNotFound
extends Exception {

	public function
	__Construct(string $Path) {
		parent::__Construct("dir not found: {$Path}");
		return;
	}

}
