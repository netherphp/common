<?php

namespace Nether\Common\Error;

use Exception;

class MethodNotFound
extends Exception {

	public function
	__Construct(string $Name) {
		parent::__Construct("method {$Name} not found");
		return;
	}

}
