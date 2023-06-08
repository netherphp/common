<?php

namespace Nether\Common;

use Ramsey;

class UUID {

	static public function
	V4():
	string {

		return Ramsey\Uuid\Uuid::UUID4()->ToString();
	}

	static public function
	V7():
	string {

		return Ramsey\Uuid\Uuid::UUID7()->ToString();
	}

}
