<?php

namespace Nether\Common;

use Nether\Common\Struct\DatafilterItem;

#[Meta\DateAdded('2022-02-11')]
class Datafilters {

	use
	Package\DatafilterPackage;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	/**
	* @codeCoverageIgnore
	*/
	#[Meta\Deprecated('2023-07-11', 'use Filters\Misc::Nullable')]
	static public function
	Nullable(mixed $Item):
	mixed {

		return Filters\Misc::Nullable($Item);
	}

	/**
	* @codeCoverageIgnore
	*/
	#[Meta\Deprecated('2023-07-11', 'use Filters\Numbers::IntType')]
	static public function
	TypeInt(mixed $Item):
	int {

		return Filters\Numbers::IntType($Item);
	}

	/**
	* @codeCoverageIgnore
	*/
	#[Meta\Deprecated('2023-07-11', 'use Filters\Numbers::IntNullable')]
	static public function
	TypeIntNullable(mixed $Item):
	?int {

		return Filters\Numbers::IntNullable($Item);
	}

	/**
	* @codeCoverageIgnore
	*/
	#[Meta\Deprecated('2023-07-11', 'use Filters\Numbers::IntRange')]
	static public function
	TypeIntRange(mixed $Input, int $Min=PHP_INT_MIN, int $Max=PHP_INT_MAX, ?int $Or=NULL):
	int {

		return Filters\Numbers::IntRange($Input, $Min, $Max, $Or);
	}

	/**
	* @codeCoverageIgnore
	*/
	#[Meta\Deprecated('2023-07-11', 'use Filters\Numbers::FloatType')]
	static public function
	TypeFloat(mixed $Item):
	float {

		return Filters\Numbers::FloatType($Item);
	}

	/**
	* @codeCoverageIgnore
	*/
	#[Meta\Deprecated('2023-07-11', 'use Filters\Numbers::FloatNullable')]
	static public function
	TypeFloatNullable(mixed $Item):
	?float {

		return Filters\Numbers::FloatNullable($Item);
	}

	/**
	* @codeCoverageIgnore
	*/
	#[Meta\Deprecated('2023-07-11', 'use Filters\Numbers::BoolType')]
	static public function
	TypeBool(mixed $Item):
	bool {

		return Filters\Numbers::BoolType($Item);
	}

	/**
	* @codeCoverageIgnore
	*/
	#[Meta\Deprecated('2023-07-11', 'use Filters\Numbers::BoolNullable')]
	static public function
	TypeBoolNullable(mixed $Item):
	?bool {

		return Filters\Numbers::BoolNullable($Item);
	}

	/**
	* @codeCoverageIgnore
	*/
	#[Meta\Deprecated('2023-07-11', 'use Filters\Numbers::Page')]
	static public function
	PageNumber(mixed $Item):
	int {

		return Filters\Numbers::Page($Item);
	}

	/**
	* @codeCoverageIgnore
	*/
	#[Meta\Deprecated('2023-07-11', 'use Filters\Links::WebsiteURL')]
	static public function
	WebsiteURL($Val):
	string {

		return Filters\Links::WebsiteURL($Val);
	}

	/**
	* @codeCoverageIgnore
	*/
	#[Meta\Deprecated('2023-07-11', 'use Filters\Links::FacebookURL')]
	static public function
	FacebookURL($Val):
	string {

		return Filters\Links::FacebookURL($Val);
	}

	/**
	* @codeCoverageIgnore
	*/
	#[Meta\Deprecated('2023-07-11', 'use Filters\Links::TwitterURL')]
	static public function
	TwitterURL($Val):
	string {

		return Filters\Links::TwitterURL($Val);
	}

	/**
	* @codeCoverageIgnore
	*/
	#[Meta\Deprecated('2023-07-11', 'use Filters\Links::InstagramURL')]
	static public function
	InstagramURL($Val):
	string {

		return Filters\Links::InstagramURL($Val);
	}

	/**
	* @codeCoverageIgnore
	*/
	#[Meta\Deprecated('2023-07-11', 'use Filters\Links::TikTokURL')]
	static public function
	TikTokURL($Val):
	string {

		return Filters\Links::TikTokURL($Val);
	}

	/**
	* @codeCoverageIgnore
	*/
	#[Meta\Deprecated('2023-07-11', 'use Filters\Links::YouTubeURL')]
	static public function
	YouTubeURL($Val):
	string {

		return Filters\Links::YouTubeURL($Val);
	}

	/**
	* @codeCoverageIgnore
	*/
	#[Meta\Deprecated('2023-07-11', 'use Filters\Links::YouTubeURL')]
	static public function
	ArrayOf(mixed $Input, callable|iterable $Filter=NULL):
	array {

		return Filters\Lists::ArrayOf($Input, $Filter);
	}

	/**
	* @codeCoverageIgnore
	*/
	#[Meta\Deprecated('2023-07-11', 'use Filters\Links::YouTubeURL')]
	static public function
	ArrayOfNullable(mixed $Input, callable|iterable $Filter=NULL):
	?array {

		return Filters\Lists::ArrayOfNullable($Input, $Filter);
	}

	/**
	* @codeCoverageIgnore
	*/
	#[Meta\Deprecated('2023-07-11', 'use Filters\Text::StringType')]
	static public function
	TypeString(mixed $Item):
	string {

		return Filters\Text::StringType($Item);
	}

	/**
	* @codeCoverageIgnore
	*/
	#[Meta\Deprecated('2023-07-11', 'use Filters\Text::StringNullable')]
	static public function
	TypeStringNullable(mixed $Item):
	?string {

		return Filters\Text::StringNullable($Item);
	}

	/**
	* @codeCoverageIgnore
	*/
	#[Meta\Deprecated('2023-07-11', 'use Filters\Text::Base64Encode')]
	static public function
	Base64Encode(mixed $Val):
	string {

		return Filters\Text::Base64Encode($Val);
	}

	/**
	* @codeCoverageIgnore
	*/
	#[Meta\Deprecated('2023-07-11', 'use Filters\Text::Base64Decode')]
	static public function
	Base64Decode(mixed $Val):
	string {

		return Filters\Text::Base64Decode($Val);
	}

	/**
	* @codeCoverageIgnore
	*/
	#[Meta\Deprecated('2023-07-11', 'use Filters\Text::Trimmed')]
	static public function
	TrimmedText(mixed $Item):
	string {

		return Filters\Text::Trimmed($Item);
	}

	/**
	* @codeCoverageIgnore
	*/
	#[Meta\Deprecated('2023-07-11', 'use Filters\Text::TrimmedNullable')]
	static public function
	TrimmedTextNullable(mixed $Item):
	?string {

		return Filters\Text::TrimmedNullable($Item);
	}

	/**
	* @codeCoverageIgnore
	*/
	#[Meta\Deprecated('2023-07-11', 'use Filters\Text::Encoded')]
	static public function
	EncodedText(mixed $Item):
	string {

		return Filters\Text::Encoded($Item);
	}

	/**
	* @codeCoverageIgnore
	*/
	#[Meta\Deprecated('2023-07-11', 'use Filters\Text::Stripped')]
	static public function
	StrippedText(mixed $Item):
	string {

		return Filters\Text::Stripped($Item);
	}

	/**
	* @codeCoverageIgnore
	*/
	#[Meta\Deprecated('2023-07-11', 'use Filters\Text::Email')]
	static public function
	Email(mixed $Item):
	?string {

		return Filters\Text::Email($Item);
	}

	/**
	* @codeCoverageIgnore
	*/
	#[Meta\Deprecated('2023-07-11', 'use Filters\Text::UUID')]
	static public function
	UUID(mixed $Item):
	?string {

		return Filters\Text::UUID($Item);
	}

	/**
	* @codeCoverageIgnore
	*/
	#[Meta\Deprecated('2023-07-11', 'use Filters\Text::PathableKey')]
	static public function
	PathableKey(mixed $Input):
	string {

		return Filters\Text::PathableKey($Input);
	}

	/**
	* @codeCoverageIgnore
	*/
	#[Meta\Deprecated('2023-07-11', 'use Filters\Text::SlottableKey')]
	static public function
	SlottableKey(mixed $Input):
	string {

		return Filters\Text::SlottableKey($Input);
	}

	/**
	* @codeCoverageIgnore
	*/
	#[Meta\Deprecated('2023-07-11', 'use Filters\Text::PascalFromKey')]
	static public function
	PascalFromKey(mixed $Input):
	string {

		return Filters\Text::PascalFromKey($Input);
	}

}
