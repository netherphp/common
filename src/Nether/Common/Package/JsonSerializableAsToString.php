<?php

namespace Nether\Common\Package;

trait JsonSerializableAsToString {

	public function
	JsonSerialize():
	string {

		return $this->ToString();
	}

};
