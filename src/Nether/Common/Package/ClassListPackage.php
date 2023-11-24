<?php

namespace Nether\Common\Package;

use Nether\Common;

#[Common\Meta\Date('2023-11-24')]
#[Common\Meta\Info('Provided a public Classes datastore property and some helper methods.')]
trait ClassListPackage {

	#[Common\Meta\Date('2023-11-24')]
	#[Common\Meta\PropertyFactory('FromArray')]
	public array|Common\Datastore
	$Classes = [];

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	GetClassesForHTML(?string $Before=NULL, ?string $After=NULL):
	string {

		$Output = $this->Classes->Join(' ');

		////////

		// this may seem stupid but it also seems to be the fastest
		// in regards to fewest buffer resizes, copies, and other pretty
		// things that could have been done instead.

		if($Before && $After)
		return "{$Before} {$Output} {$After}";

		if($Before)
		return "{$Before} {$Output}";

		if($After)
		return "{$Output} {$After}";

		////////

		return $Output;
	}

};
