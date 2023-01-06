<?php

namespace Nether\Common;

use Ramsey;

class UUID {

	static public function
	V4():
	string {

		return Ramsey\Uuid\Uuid::UUID4()->ToString();
	}

}
