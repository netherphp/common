<?php

namespace Nether\Common\Error;

use Exception;

class FormatInvalidJSON
extends Exception {

	public function
	__Construct(?string $Error=NULL) {

		if($Error === NULL)
		$Error = sprintf(
			'%s (%d)',
			json_last_error_msg(),
			json_last_error()
		);

		parent::__Construct($Error);

		return;
	}

}
