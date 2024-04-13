<?php

namespace Nether\Common\Error;

use Exception;

class FileReadError
extends Exception {

	public function
	__Construct(string $Filename) {
		parent::__Construct("file {$Filename} suffered read error");
		return;
	}

}
