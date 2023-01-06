<?php

namespace Nether\Common;

use Nether\Object\Struct\DatafilterItem;

class Datafilters {

	////////////////////////////////////////////////////////////////
	// primative type filters //////////////////////////////////////

	static public function
	TypeInt(mixed $Item):
	int {

		if($Item instanceof DatafilterItem)
		$Item = $Item->Value;

		return (int)$Item;
	}

	static public function
	TypeIntNullable(mixed $Item):
	?int {

		if($Item instanceof DatafilterItem)
		$Item = $Item->Value;

		if(!$Item)
		return NULL;

		return (int)$Item;
	}

	static public function
	TypeFloat(mixed $Item):
	int {

		if($Item instanceof DatafilterItem)
		$Item = $Item->Value;

		return (float)$Item;
	}

	static public function
	TypeFloatNullable(mixed $Item):
	?int {

		if($Item instanceof DatafilterItem)
		$Item = $Item->Value;

		if(!$Item)
		return NULL;

		return (float)$Item;
	}

	static public function
	TypeBool(mixed $Item):
	bool {

		if($Item instanceof DatafilterItem)
		$Item = $Item->Value;

		// scale back the number of things we need to test for.

		$Item = strtoupper(trim(
			(string)($Item ?: '')
		));

		return match($Item) {
			'1', 'T', 'TRUE', 'Y', 'YES'
			=> TRUE,

			default
			=> FALSE
		};
	}

	static public function
	TypeBoolNullable(mixed $Item):
	?bool {

		if($Item instanceof DatafilterItem)
		$Item = $Item->Value;

		if(!$Item)
		return NULL;

		$Item = strtoupper(trim(
			(string)($Item ?: '')
		));

		return match($Item) {
			'1', 'T', 'TRUE', 'Y', 'YES'
			=> TRUE,

			default
			=> FALSE
		};
	}

	static public function
	TypeString(mixed $Item):
	string {

		if($Item instanceof DatafilterItem)
		$Item = $Item->Value;

		return (string)$Item;
	}

	static public function
	TypeStringNullable(mixed $Item):
	?string {

		if($Item instanceof DatafilterItem)
		$Item = $Item->Value;

		if(!$Item)
		return NULL;

		return (string)$Item ?: NULL;
	}

	////////////////////////////////////////////////////////////////
	// generic text filters ////////////////////////////////////////

	static public function
	Base64Encode(mixed $Val):
	string {
	/*//
	encode into base64 safe for urls omitting the trailing padding as it is
	not needed tbh.
	https://en.wikipedia.org/wiki/Base64#URL_applications
	//*/

		if($Val instanceof DatafilterItem)
		$Val = $Val->Value;

		return str_replace(
			['+','/'],
			['-','_'],
			rtrim(base64_encode($Val),'=')
		);
	}

	static public function
	Base64Decode(mixed $Val):
	string {
	/*//
	decode from url safe base64.
	https://en.wikipedia.org/wiki/Base64#URL_applications
	//*/

		if($Val instanceof DatafilterItem)
		$Val = $Val->Value;

		return base64_decode(str_replace(
			['-','_'],
			['+','/'],
			$Val ?? ''
		));
	}

	static public function
	TrimmedText(mixed $Item):
	string {
	/*//
	@date 2022-11-11
	trim whitespace from either end of the input.
	//*/

		if($Item instanceof DatafilterItem)
		$Item = $Item->Value;

		return trim((string)$Item ?: '');
	}

	static public function
	TrimmedTextNullable(mixed $Item):
	?string {
	/*//
	@date 2022-11-11
	trim whitespace from either end of the input. returns null if the result
	is falsy.
	//*/

		if($Item instanceof DatafilterItem)
		$Item = $Item->Value;

		if(!$Item)
		return NULL;

		return trim($Item) ?: NULL;
	}

	static public function
	Email(mixed $Item):
	string {
	/*//
	@date 2022-11-14
	//*/

		if($Item instanceof DatafilterItem)
		$Item = $Item->Value;

		return strtolower(filter_var(
			trim($Item),
			FILTER_VALIDATE_EMAIL,
			[ 'options' => [ 'default' => '' ]]
		));
	}

	static public function
	PathableKey(mixed $Input):
	string {
	/*//
	utility method i have in almost all of my projects to take input uris and
	spit out versions that would break anything if we tried to use it in a
	file path. so its a super sanitiser only allowing alphas, numerics,
	dashes, periods, and forward slashes. does not allow dot stacking
	to prevent traversal foolery.
	//*/

		if($Input instanceof DatafilterItem)
		$Input = $Input->Value;

		////////

		$Output = strtolower(trim($Input));

		// allow things that could be nice clean file names.

		$Output = preg_replace(
			'#[^a-zA-Z0-9\-\/\.]#', '',
			str_replace(' ', '-', $Output)
		);

		// disallow traversal foolery.

		$Output = preg_replace(
			'#[\.]{2,}#', '',
			$Output
		);

		$Output = preg_replace(
			'#[\/]{2,}#', '/',
			$Output
		);

		return $Output;
	}

	static public function
	PathableKeySingle(mixed $Input):
	string {

		if($Input instanceof DatafilterItem)
		$Input = $Input->Value;

		////////

		$Output = strtolower(trim($Input));

		// allow things that could be nice clean file names.

		$Output = preg_replace(
			'#[^a-zA-Z0-9\-\.]#', '',
			str_replace(' ', '-', $Output)
		);

		// disallow traversal foolery.

		$Output = preg_replace(
			'#[\.]{2,}#', '',
			$Output
		);

		$Output = preg_replace(
			'#[\/]{2,}#', '/',
			$Output
		);

		return $Output;
	}

}
