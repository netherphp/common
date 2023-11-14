<?php

namespace Nether\Common\Error;

use Exception;

class FileWriteError
extends Exception {

	public function
	__Construct(string $Filename) {
		parent::__Construct("file {$Filename} suffered write error");
		return;
	}

}
