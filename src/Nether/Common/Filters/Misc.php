<?php

namespace Nether\Common\Filters;

use Nether\Common;

#[Common\Meta\DateAdded('2023-07-07')]
class Misc {

	use
	Common\Package\DatafilterPackage;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	Nullable(mixed $Item):
	mixed {
	/*//
	if the input evals to FALSE, its now a NULL.
	//*/

		static::Prepare($Item);

		return $Item ?: NULL;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Common\Meta\Date('2023-11-22')]
	#[COmmon\Meta\Info('Input must be one of these else shoot back this other thing.')]
	static public function
	OneOfTheseOr(mixed $Input, array $Valids=[], mixed $Default=NULL):
	mixed {

		static::Prepare($Input);

		////////

		if(in_array($Input, $Valids, TRUE))
		return $Input;

		////////

		return $Default;
	}

	#[Common\Meta\Date('2023-11-22')]
	#[Common\Meta\Info('Input must be one of these else shoots back the first one.')]
	static public function
	OneOfTheseFirst(mixed $Input, array $Valids=[]):
	mixed {

		return static::OneOfTheseOr(
			$Input, $Valids,
			$Valids[array_key_first($Valids)]
		);
	}

	#[Common\Meta\Date('2023-11-22')]
	#[COmmon\Meta\Info('The input must be one of these else shoots back NULL.')]
	static public function
	OneOfTheseNullable(mixed $Input, array $Valids=[]):
	mixed {

		return static::OneOfTheseOr(
			$Input, $Valids,
			NULL
		);
	}

}
