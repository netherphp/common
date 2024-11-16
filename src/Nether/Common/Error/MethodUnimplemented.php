<?php

namespace Nether\Common\Error;

use Exception;

class MethodUnimplemented
extends Exception {

	public function
	__Construct(string $Class, string $Name) {

		parent::__Construct(sprintf(
			'%s::%s must be implemented',
			$Class, $Name
		));

		return;
	}

}
