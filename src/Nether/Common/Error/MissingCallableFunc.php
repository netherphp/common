<?php

namespace Nether\Common\Error;

use Exception;

class MissingCallableFunc
extends Exception {

	public function
	__Construct() {
		parent::__Construct('No callable functions were supplied');
		return;
	}

}
