<?php

namespace Nether\Common\Error;

class FormatInvalidCrontab
extends FormatInvalid {

	public function
	__Construct(?string $Input=NULL) {
		parent::__Construct("crontab format invalid: {$Input}");
		return;
	}

}
