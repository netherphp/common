<?php

namespace Nether\Common\Package;

trait JsonSerializableAsToJson {

	public function
	JsonSerialize():
	string {

		return $this->ToJSON();
	}

};
