<?php

namespace Nether\Common\Filters;

use Nether\Common;

#[Common\Meta\DateAdded('2023-07-11')]
class Numbers {

	use
	Common\Package\DatafilterPackage;

	////////////////////////////////////////////////////////////////
	// CONVERT TO INTEGER //////////////////////////////////////////

	#[Common\Meta\DateAdded('2022-02-17')]
	#[Common\Meta\Info('Typecast to Integer, with all the PHP pitfalls that may include.')]
	static public function
	IntType(mixed $Item):
	int {

		static::Prepare($Item);

		return (int)$Item;
	}

	#[Common\Meta\DateAdded('2022-02-17')]
	#[Common\Meta\Info('Typecast to Integer, except falsey values turn into NULL.')]
	static public function
	IntNullable(mixed $Item):
	?int {

		static::Prepare($Item);

		if(!$Item)
		return NULL;

		return (int)$Item;
	}

	#[Common\Meta\DateAdded('2022-02-17')]
	#[Common\Meta\Info('Typecast to Integer, capping within specified range (Or this other specified value).')]
	static public function
	IntRange(mixed $Input, int $Min=PHP_INT_MIN, int $Max=PHP_INT_MAX, ?int $Or=NULL):
	int {

		$Input = static::IntType(static::Prepare($Input));

		if($Or !== NULL && $Input === $Or)
		return $Input;

		return max($Min, min($Max, $Input));
	}

	#[Common\Meta\DateAdded('2023-07-11')]
	#[Common\Meta\Info('String to Int. Supports decimal, octal 0o, hex 0x, and binary 0b.')]
	static public function
	IntFromNumeric(mixed $Val):
	int {

		static::Prepare($Val);

		if(is_int($Val))
		return $Val;

		if(is_string($Val))
		$Val = match(TRUE) {
			str_starts_with($Val, '0o'),
			=> octdec($Val),

			str_starts_with($Val, '0x')
			=> hexdec($Val),

			str_starts_with($Val, '0b')
			=> bindec($Val),

			default
			=> static::IntType($Val)
		};

		return (int)$Val;
	}

	////////////////////////////////////////////////////////////////
	// CONVERT TO FLOAT ////////////////////////////////////////////

	#[Common\Meta\DateAdded('2022-02-17')]
	#[Common\Meta\Info('Typecast to Float, with all the PHP pitfalls that may include.')]
	static public function
	FloatType(mixed $Item):
	float {

		static::Prepare($Item);

		return (float)$Item;
	}

	#[Common\Meta\DateAdded('2022-02-17')]
	#[Common\Meta\Info('Typecast to Float, except falsey values turn into NULL.')]
	static public function
	FloatNullable(mixed $Item):
	?float {

		static::Prepare($Item);

		if(!$Item)
		return NULL;

		return (float)$Item;
	}

	////////////////////////////////////////////////////////////////
	// CONVERT TO BOOLEAN //////////////////////////////////////////

	#[Common\Meta\DateAdded('2022-11-18')]
	#[Common\Meta\Info('Typecast to Boolean. String values 1, T, TRUE, Y, and YES are treated as TRUE. Anything else is FALSE.')]
	static public function
	BoolType(mixed $Item):
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

	#[Common\Meta\DateAdded('2022-11-18')]
	#[Common\Meta\Info('Typecast to Boolean. Same as BoolType adding string and literal NULL returns NULL.')]
	static public function
	BoolNullable(mixed $Item):
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

	////////////////////////////////////////////////////////////////
	// MISC - ESOTERIC /////////////////////////////////////////////

	#[Common\Meta\DateAdded('2022-02-17')]
	#[Common\Meta\Info('Always returns an Int within the range 1 to INF.')]
	static public function
	Page(mixed $Item):
	int {

		$Item = static::IntType(static::Prepare($Item));

		if($Item < 1)
		return 1;

		return $Item;
	}

}
