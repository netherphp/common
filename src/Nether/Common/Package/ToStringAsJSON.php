<?php

namespace Nether\Common\Package;

use Nether\Common;

trait ToStringAsJSON {

	public function
	ToString():
	string {

		if($this instanceof Common\Interfaces\ToJSON)
		return $this->ToJSON();

		if($this instanceof Common\Interfaces\ToArray)
		return Common\Filters\Text::ReadableJSON($this->ToArray());

		return Common\Filters\Text::ReadableJSON($this);
	}

};
