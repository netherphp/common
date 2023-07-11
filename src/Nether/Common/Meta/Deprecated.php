<?php

namespace Nether\Common\Meta;

use Attribute;

#[Attribute]
class Deprecated {

	protected string
	$Date;

	protected ?string
	$Info;

	public function
	__Construct(string $Date, string $Info=NULL) {

		$this->Date = $Date;
		$this->Info = $Info;

		return;
	}

}
