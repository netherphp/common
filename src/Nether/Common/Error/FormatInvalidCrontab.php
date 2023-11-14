<?php

namespace Nether\Common\Error;

use Exception;

class FormatInvalidCrontab
extends Exception {

	public function
	__Construct(?string $Input=NULL) {
		parent::__Construct("crontab format invalid: {$Input}");
		return;
	}

}
