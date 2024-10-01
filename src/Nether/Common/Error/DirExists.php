<?php

namespace Nether\Common\Error;

use Exception;

class DirExists
extends Exception {

	public function
	__Construct(string $Path) {
		parent::__Construct("dir already exists: {$Path}");
		return;
	}

}
