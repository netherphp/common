<?php

namespace Nether\Common\Package;

use Nether\Common;

#[Common\Meta\Date('2023-11-23')]
#[Common\Meta\Info('Most generic of New.')]
trait PrototypeNew {

	// this mainly exists to quickly glue onto a class in the event the
	// transition to Prototype's base class being Newless is rougher than
	// expected.

	static public function
	New():
	static {

		return new static(
			func_get_args(),
			NULL,
			Common\Prototype\Flags::StrictInput
		);
	}

};
