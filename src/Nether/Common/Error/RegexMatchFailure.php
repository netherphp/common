<?php

namespace Nether\Common\Error;
use Nether\Common;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

#[Common\Meta\Date('2024-06-22')]
class RegexMatchFailure
extends Common\Error {

	public ?string
	$Pattern;

	public mixed
	$Data;

	public function
	__Construct(?string $Pattern=NULL, mixed $Data=NULL) {
		parent::__Construct("regex match fail: {$Pattern}");

		$this->Pattern = $Pattern;
		$this->Data = $Data;

		return;
	}

};
