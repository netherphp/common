<?php

namespace Nether\Common\Error;

use Exception;

class RandomGeneratorNotSeeded
extends Exception {

	public function
	__Construct() {
		parent::__Construct('rng not seeded yet');
		return;
	}

}
