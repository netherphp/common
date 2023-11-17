<?php

namespace Nether\Common\Error;

use Nether\Common;

#[Common\Meta\Date('2023-11-16')]
class FileNotFound
extends Common\Error {

	public readonly string
	$Filename;

	public function
	__Construct(string $Filename) {
		parent::__Construct("file not found: {$Filename}");

		$this->Filename = $Filename;

		return;
	}

};
