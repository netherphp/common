<?php

namespace Nether\Common\Package;

trait StringableAsToJSON {

	public function
	__ToString():
	string {

		return $this->ToJSON();
	}

};
