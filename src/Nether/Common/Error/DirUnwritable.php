<?php

namespace Nether\Common\Error;

use Exception;

class DirUnwritable
extends Exception {

	public function
	__Construct(string $Dirname) {
		parent::__Construct("dir {$Dirname} is unwritable");
		return;
	}

}
