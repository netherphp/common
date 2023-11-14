<?php

namespace Nether\Common\Package;

use Nether\Common;

trait ToString {

	public function
	ToString():
	string {

		return sprintf(
			'%s(%s)',
			$this::class,
			spl_object_id($this)
		);
	}

};
