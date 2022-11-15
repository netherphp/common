<?php

namespace Nether\Common;

use Nether\Object\Struct\DatafilterItem;

class Datafilters {

	////////////////////////////////////////////////////////////////
	// primative type filters //////////////////////////////////////

	static public function
	TypeString(DatafilterItem $Item):
	string {
	/*//
	@date 2022-11-11
	makes sure the result is a string.
	//*/

		return (string)$Item->Value;
	}

	static public function
	TypeStringNullable(DatafilterItem $Item):
	?string {
	/*//
	@date 2022-11-11
	makes sure the result is a string. returns null if the result is falsy.
	//*/

		if(!$Item->Value)
		return NULL;

		return (string)$Item->Value ?: NULL;
	}

	////////////////////////////////////////////////////////////////
	// generic text filters ////////////////////////////////////////

	static public function
	Base64Encode($Val):
	string {
	/*//
	encode into base64 safe for urls omitting the trailing padding as it is
	not needed tbh.
	https://en.wikipedia.org/wiki/Base64#URL_applications
	//*/

		return str_replace(
			['+','/'],
			['-','_'],
			rtrim(base64_encode($Val),'=')
		);
	}

	static public function
	Base64Decode($Val):
	string {
	/*//
	decode from url safe base64.
	https://en.wikipedia.org/wiki/Base64#URL_applications
	//*/

		return base64_decode(str_replace(
			['-','_'],
			['+','/'],
			$Val ?? ''
		));
	}

	static public function
	TrimmedText(string|DatafilterItem $Item):
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
	TrimmedTextNullable(string|DatafilterItem $Item):
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
	Email(string|DatafilterItem $Item):
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
