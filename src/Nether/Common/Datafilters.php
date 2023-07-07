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
	TypeIntRange(mixed $Input, int $Min=PHP_INT_MIN, int $Max=PHP_INT_MAX, ?int $Or=NULL):
	int {

		$Input = static::TypeInt(static::Prepare($Input));

		if($Or !== NULL && $Input === $Or)
		return $Input;

		return max($Min, min($Max, $Input));
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

	static public function
	PageNumber(mixed $Item):
	int {

		$Item = static::TypeInt(static::Prepare(
			$Item
		));

		if($Item <= 0)
		return 1;

		return $Item;
	}

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

		static::Prepare($Item);

		return trim($Item ?: '') ?: NULL;
	}

	static public function
	EncodedText(mixed $Item):
	string {
	/*//
	@date 2020-06-01
	//*/

		static::Prepare($Item);

		return htmlspecialchars(trim($Item));
	}

	static public function
	StrippedText(mixed $Item):
	string {
	/*//
	@date 2020-06-01
	//*/

		static::Prepare($Item);

		return strip_tags(trim($Item));
	}

	static public function
	Email(mixed $Item):
	?string {
	/*//
	@date 2022-11-14
	//*/

		static::Prepare($Item);

		return strtolower(filter_var(
			trim($Item ?: ''),
			FILTER_VALIDATE_EMAIL,
			[ 'options' => [ 'default' => '' ]]
		)) ?: NULL;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	WebsiteURL($Val):
	string {

		return Filters\Links::WebsiteURL($Val);
	}

	static public function
	FacebookURL($Val):
	string {

		return Filters\Links::FacebookURL($Val);
	}

	static public function
	TwitterURL($Val):
	string {

		return Filters\Links::TwitterURL($Val);
	}

	static public function
	InstagramURL($Val):
	string {

		return Filters\Links::InstagramURL($Val);
	}

	static public function
	TikTokURL($Val):
	string {

		return Filters\Links::TikTokURL($Val);
	}

	static public function
	YouTubeURL($Val):
	string {

		return Filters\Links::YouTubeURL($Val);
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	UUID(mixed $Item):
	?string {

		static::Prepare($Item);

		if(!$Item || strlen($Item) !== 36)
		return NULL;

		if(preg_match('/[^a-fA-F0-9\-]/', $Item))
		return NULL;

		return $Item;
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

		$Output = strtolower(trim(static::Prepare($Input) ?? ''));

		// allow things that could be nice clean file names.

		$Output = preg_replace(
			'#[^a-zA-Z0-9\-\/\.]#', '',
			str_replace(' ', '-', $Output)
		);

		// disallow traversal foolery.

		$Output = preg_replace(
			'#[\.]{2,}#', '.',
			$Output
		);

		$Output = preg_replace(
			'#[\-]{2,}#', '-',
			$Output
		);

		$Output = preg_replace(
			'#(?:\.*/-*)|(?:-*/-*)#', '/',
			$Output
		);

		$Output = preg_replace(
			'#(?:[/]{2,})#', '/',
			$Output
		);

		return $Output;
	}

	static public function
	SlottableKey(mixed $Input):
	string {
	/*//
	same as pathable key except drops pathability by replacing all forward
	slashes with dashes.
	//*/

		$Output = static::PathableKey($Input);
		$Output = str_replace('/', '-', $Output);

		return $Output;
	}

	static public function
	PascalFromKey(mixed $Input):
	string {
	/*//
	generate a pascal formatted thing from a key formatted thing.
	//*/

		static::Prepare($Input);

		$Output = preg_replace('#([A-Z])#', ' \\1', $Input);
		$Output = static::SlottableKey($Output);
		$Output = ucwords(str_replace('-', ' ', $Output));
		$Output = preg_replace('#[^A-Za-z0-9]#', '', $Output);

		return $Output;
	}

	static public function
	ArrayOf(mixed $Input, callable|iterable $Filter=NULL):
	array {
	/*//
	@date 2023-07-07
	makes sure what was given to it was a list of items. if it was just a
	single item turn it into a list of items. optionally if a list of filters
	is provided, run them on each item of this array.
	//*/

		$Output = static::Prepare($Input);
		$Func = NULL;

		////////

		if(!is_array($Output))
		$Output = [ $Output ];

		if(!is_iterable($Filter))
		$Filter = [ $Filter ];

		foreach($Filter as $Func)
		if(is_callable($Func))
		$Output = array_map($Func(...), $Output);

		////////

		return $Output;
	}

	static public function
	ArrayOfNullable(mixed $Input, callable|iterable $Filter=NULL):
	?array {
	/*//
	@date 2023-07-07
	if given null it will return null instead of an array with an null.
	//*/

		$Output = static::Prepare($Input);

		if($Output === NULL)
		return NULL;

		return static::ArrayOf($Input, $Filter);
	}

}
