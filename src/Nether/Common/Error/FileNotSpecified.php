<?php

namespace Nether\Common\Error;

use Exception;

class FileNotSpecified
extends Exception {

	public function
	__Construct() {
		parent::__Construct("file not specified");
		return;
	}

}
