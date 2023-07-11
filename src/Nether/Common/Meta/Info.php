<?php

namespace Nether\Common\Meta;

use Attribute;

#[Attribute]
class Info {

	protected ?string
	$Text;

	public function
	__Construct(string $Text) {

		$this->Text = $Text;

		return;
	}

}
