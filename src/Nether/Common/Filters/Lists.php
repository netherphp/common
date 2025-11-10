<?php

namespace Nether\Common\Filters;

use Nether\Common;

#[Common\Meta\DateAdded('2023-07-07')]
class Lists {

	use
	Common\Package\DatafilterPackage;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Common\Meta\DateAdded('2023-07-07')]
	#[Common\Meta\Info('Run a set of filters over a list of items. If a single item is provided it will be wrapped in an array.')]
	static public function
	ArrayOf(mixed $Items, callable|iterable|NULL $Filters=NULL):
	array {

		$Output = static::Prepare($Items);
		$Func = NULL;

		// make sure we have something we can loop over.

		if(!is_iterable($Output))
		$Output = [ $Output ];

		if(!is_array($Output))
		$Output = iterator_to_array($Output);

		// make sure we have a list of filters then run them.

		if(!is_iterable($Filters))
		$Filters = [ $Filters ];

		foreach($Filters as $Func)
		if(is_callable($Func))
		$Output = array_map($Func(...), $Output);

		////////

		return $Output;
	}

	#[Common\Meta\DateAdded('2023-07-07')]
	#[Common\Meta\Info('Same as ArrayOf except Falsy values will return NULL.')]
	static public function
	ArrayOfNullable(mixed $Items, callable|iterable|NULL $Filters=NULL):
	?array {

		$Output = static::Prepare($Items);

		////////

		// no list at all? nope.

		if(!$Output)
		return NULL;

		// empty list? nope.

		if(is_countable($Output) && count($Output) === 0)
		return NULL;

		////////

		return static::ArrayOf($Output, $Filters);
	}

	#[Common\Meta\DateAdded('2023-07-07')]
	#[Common\Meta\Info('Same as ArrayOf except Falsy values will return NULL.')]
	static public function
	CommaOfNullable(mixed $Items, callable|iterable|NULL $Filters=NULL):
	?array {

		$Output = static::Prepare($Items);

		////////

		// no list at all? nope.

		if(!$Output)
		return NULL;

		if(is_string($Output))
		$Output = explode(',', $Output);

		// empty list? nope.

		if(is_countable($Output) && count($Output) === 0)
		return NULL;

		////////

		$Output = array_map(trim(...), $Output);

		return static::ArrayOf($Output, $Filters);
	}

}
