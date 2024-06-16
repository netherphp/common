<?php

namespace Nether\Common\Package;

use Nether\Common;

trait ToJSON {

	public function
	ToJSON():
	string {

		if($this instanceof Common\Interfaces\ToArray)
		return Common\Filters\Text::ReadableJSON($this->ToArray());

		return Common\Filters\Text::ReadableJSON($this);
	}

};
