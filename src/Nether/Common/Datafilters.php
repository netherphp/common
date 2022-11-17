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
	TypeString(mixed $Item):
	string {
	/*//
	@date 2022-11-11
	makes sure the result is a string.
	//*/

		if($Item instanceof DatafilterItem)
		$Item = $Item->Value;

		return (string)$Item;
	}

	static public function
	TypeStringNullable(mixed $Item):
	?string {
	/*//
	@date 2022-11-11
	makes sure the result is a string. returns null if the result is falsy.
	//*/

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

		return trim((string)$Item->Value ?: '');
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

}
