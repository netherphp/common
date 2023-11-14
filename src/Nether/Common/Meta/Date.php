<?php

namespace Nether\Common\Meta;

use Attribute;

#[Attribute]
class Date {

	protected string
	$Date;

	public function
	__Construct(string $Date) {

		$this->Date = $Date;

		return;
	}

}
