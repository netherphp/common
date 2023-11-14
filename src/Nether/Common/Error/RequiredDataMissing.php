<?php

namespace Nether\Common\Error;

use Exception;

class RequiredDataMissing
extends Exception {

	public function
	__Construct(string $Name, string $Type='mixed') {
		parent::__Construct("missing {$Name} ($Type)");
		return;
	}

}
