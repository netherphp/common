<?php

namespace Nether\Common\Error;

use Exception;

class FormatInvalid
extends Exception {

	public function
	__Construct(string $FormatDesc='something different') {
		parent::__Construct("invalid format (expected {$FormatDesc})");
		return;
	}

}
