<?php

namespace Nether\Common;

use Ramsey;

class UUID {

	static public function
	V4():
	string {

		return Ramsey\Uuid\Uuid::UUID4()->ToString();
	}

	static public function
	V7():
	string {

		return Ramsey\Uuid\Uuid::UUID7()->ToString();
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Meta\Date('2023-08-06')]
	#[Meta\Info('Quickly get a UUID based ID name for HTML elements.')]
	static public function
	ForElementHTML(string $Prefix='el'):
	string {

		// one thing i found out a while back was that some browers not all
		// are more strict on what is likely the spec, and it turned out
		// that when i just dumped uuid as an element id they would not work
		// at random times and it was if it started with a number. so then
		// i started prefixing them but like nothing about the uuid format
		// matters it just needs to be unique so i thought maybe to just do
		// some smush.

		// im typically only really passing these around within template
		// area scripts for things that might need to be able to handle
		// multiple of the same type of object on a page.

		$Output = sprintf(
			'%s-%s',
			$Prefix,
			str_replace('-', '', static::V7())
		);

		return $Output;
	}

}
