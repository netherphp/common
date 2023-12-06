<?php

namespace Nether\Common\Package;

trait JsonSerializableAsToJSON {

	public function
	JsonSerialize():
	string {

		return $this->ToJSON();
	}

};
