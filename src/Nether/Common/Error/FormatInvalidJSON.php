<?php

namespace Nether\Common\Error;

class FormatInvalidJSON
extends FormatInvalid {

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
