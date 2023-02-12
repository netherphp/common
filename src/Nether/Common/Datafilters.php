<?php

namespace Nether\Common;

use Nether\Common\Struct\DatafilterItem;

class Datafilters {

	static public function
	Prepare(mixed &$Item):
	mixed {

		if($Item instanceof DatafilterItem)
		$Item = $Item->Value;

		return $Item;
	}

	////////////////////////////////////////////////////////////////
	// primative type filters //////////////////////////////////////

	static public function
	TypeInt(mixed $Item):
	int {

		static::Prepare($Item);

		return (int)$Item;
	}

	static public function
	TypeIntNullable(mixed $Item):
	?int {

		static::Prepare($Item);

		if(!$Item)
		return NULL;

		return (int)$Item;
	}

	static public function
	TypeFloat(mixed $Item):
	float {

		static::Prepare($Item);

		return (float)$Item;
	}

	static public function
	TypeFloatNullable(mixed $Item):
	?float {

		static::Prepare($Item);

		if(!$Item)
		return NULL;

		return (float)$Item;
	}

	static public function
	TypeBool(mixed $Item):
	bool {

		static::Prepare($Item);

		if(is_bool($Item))
		return $Item;

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

		static::Prepare($Item);

		if(is_bool($Item))
		return $Item;

		if($Item === NULL)
		return NULL;

		$Item = strtoupper(trim(
			(string)($Item ?: '')
		));

		return match($Item) {
			'1', 'T', 'TRUE', 'Y', 'YES'
			=> TRUE,

			'NULL',
			=> NULL,

			default
			=> FALSE
		};
	}

	static public function
	TypeString(mixed $Item):
	string {

		static::Prepare($Item);

		return (string)$Item;
	}

	static public function
	TypeStringNullable(mixed $Item):
	?string {

		static::Prepare($Item);

		if($Item === NULL)
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
	https://datatracker.ietf.org/doc/html/rfc4648#section-5
	//*/

		static::Prepare($Val);

		return str_replace(
			['+', '/'],
			['-', '_'],
			rtrim(base64_encode($Val), '=')
		);
	}

	static public function
	Base64Decode(mixed $Val):
	string {
	/*//
	decode from url safe base64.
	https://en.wikipedia.org/wiki/Base64#URL_applications
	//*/

		static::Prepare($Val);

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

		static::Prepare($Item);

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
	EncodedText(mixed $Item):
	string {
	/*//
	@date 2020-06-01
	//*/

		if($Item instanceof DatafilterItem)
		$Item = $Item->Value;

		return htmlspecialchars(trim($Item));
	}

	static public function
	StrippedText(mixed $Item):
	string {
	/*//
	@date 2020-06-01
	//*/

		if($Item instanceof DatafilterItem)
		$Item = $Item->Value;

		return strip_tags(trim($Item));
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

		$Output = preg_replace(
			'#[\-]{2,}#', '',
			$Output
		);

		return $Output;
	}

	static public function
	PathableKeySingle(mixed $Input):
	string {
	/*//
	same as PathableKey except it doesn't allow directory separators so its
	only cool with single "slot" keys.
	//*/

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

		$Output = preg_replace(
			'#[\-]{2,}#', '',
			$Output
		);

		return $Output;
	}

	static public function
	PascalFromKey(mixed $Input):
	string {
	/*//
	generate a pascal formatted thing from a key formatted thing.
	//*/

		if($Input instanceof DatafilterItem)
		$Input = $Input->Value;

		// drop all the unsavoury stuff and the case.

		$Output = strtolower(preg_replace('/[^a-zA-Z0-9-]/', '', $Input));

		// replace dashes with spaces and allow ucwords to recase it.

		$Output = ucwords(str_replace('-', ' ', $Output));

		// then drop the spaces. pascal case.

		$Output = str_replace(' ', '', $Output);

		return $Output;
	}

}
