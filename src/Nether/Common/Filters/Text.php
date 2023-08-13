<?php

namespace Nether\Common\Filters;

use Nether\Common;

#[Common\Meta\DateAdded('2023-07-11')]
class Text {

	use
	Common\Package\DatafilterPackage;

	////////////////////////////////////////////////////////////////
	// CONVERT TO STRING ///////////////////////////////////////////

	#[Common\Meta\DateAdded('2022-02-17')]
	#[Common\Meta\Info('Typecast to String, with all the PHP pitfalls that may include.')]
	static public function
	StringType(mixed $Item):
	string {

		static::Prepare($Item);

		return (string)$Item;
	}

	#[Common\Meta\DateAdded('2022-02-17')]
	#[Common\Meta\Info('Typecast to String, except falsey values turn into NULL.')]
	static public function
	StringNullable(mixed $Item):
	?string {

		static::Prepare($Item);

		if($Item === NULL)
		return NULL;

		return (string)$Item ?: NULL;
	}

	////////////////////////////////////////////////////////////////
	// CONVERT AND SHORE UP ////////////////////////////////////////

	#[Common\Meta\DateAdded('2022-11-11')]
	#[Common\Meta\Info('Trim whitespace from either end of the input.')]
	static public function
	Trimmed(mixed $Item):
	string {

		static::Prepare($Item);

		if(!is_string($Item))
		$Item = (string)$Item;

		return trim($Item);
	}

	#[Common\Meta\DateAdded('2022-11-11')]
	#[Common\Meta\Info('Same as Trimmed but returns NULL if the result was falsy.')]
	static public function
	TrimmedNullable(mixed $Item):
	?string {

		return static::Trimmed($Item) ?: NULL;
	}

	#[Common\Meta\DateAdded('2020-06-01')]
	#[Common\Meta\Info('Trim and Encode suitable for safe HTML output.')]
	static public function
	Encoded(mixed $Item):
	string {

		return htmlspecialchars(static::Trimmed($Item));
	}

	#[Common\Meta\DateAdded('2023-07-11')]
	#[Common\Meta\Info('Same as Encoded but returns NULL if the result was falsy.')]
	static public function
	EncodedNullable(mixed $Item):
	?string {

		return static::Encoded($Item) ?: NULL;
	}

	#[Common\Meta\DateAdded('2020-06-01')]
	#[Common\Meta\Info('Trim, Strip HTML, and Encode for safe HTML output.')]
	static public function
	Stripped(mixed $Item):
	string {

		return htmlspecialchars(strip_tags(static::Trimmed($Item)));
	}

	#[Common\Meta\DateAdded('2023-07-11')]
	#[Common\Meta\Info('Same as Stripped but returns NULL if the result was falsy.')]
	static public function
	StrippedNullable(mixed $Item):
	?string {

		return static::Stripped($Item) ?: NULL;
	}

	////////////////////////////////////////////////////////////////
	// VALIDATORS //////////////////////////////////////////////////

	#[Common\Meta\DateAdded('2022-11-14')]
	#[Common\Meta\Info('Returns an email address or NULL if PHPs built-in filter said no.')]
	static public function
	Email(mixed $Email):
	?string {

		static::Prepare($Email);

		$Email = trim($Email ?: '');
		$Opts = [ 'options' => [ 'default' => '' ]];

		$Result = strtolower(filter_var(
			$Email,
			FILTER_VALIDATE_EMAIL,
			$Opts
		));

		return $Result ?: NULL;
	}

	#[Common\Meta\DateAdded('2022-11-17')]
	#[Common\Meta\Info('Returns a UUID or NULL if it does not smell like one.')]
	static public function
	UUID(mixed $UUID):
	?string {

		static::Prepare($UUID);

		if(!$UUID || !is_string($UUID) || strlen($UUID) !== 36)
		return NULL;

		if(preg_match('/[^a-fA-F0-9\-]/', $UUID))
		return NULL;

		return $UUID;
	}

	////////////////////////////////////////////////////////////////
	// FORMAT CONVERSIONS //////////////////////////////////////////

	#[Common\Meta\DateAdded('2022-11-17')]
	#[Common\Meta\Info('Encode into Base64 for URLs. Omits trailing padding its not needed.')]
	static public function
	Base64Encode(mixed $Plain):
	string {

		static::Prepare($Plain);

		// https://en.wikipedia.org/wiki/Base64#URL_applications
		// https://datatracker.ietf.org/doc/html/rfc4648#section-5

		$Plain = rtrim(base64_encode($Plain ?? ''), '=');

		return str_replace(
			['+', '/'],
			['-', '_'],
			$Plain
		);
	}

	#[Common\Meta\DateAdded('2022-11-17')]
	#[Common\Meta\Info('Decode from Base64 for URLs.')]
	static public function
	Base64Decode(mixed $Encoded):
	string {

		static::Prepare($Encoded);

		// https://en.wikipedia.org/wiki/Base64#URL_applications

		$Encoded ??= '';

		return base64_decode(str_replace(
			['-', '_'],
			['+', '/'],
			$Encoded
		));
	}

	////////////////////////////////////////////////////////////////
	// MISC - ESOTERIC /////////////////////////////////////////////

	#[Common\Meta\DateAdded('2022-02-17')]
	#[Common\Meta\Info('Bake a string down to a version clean enough for URL slugs. Allows alpha, numeric, dash, dot, and forward slash, but it does some foolery to also make them look good and avoid directory traversal issues.')]
	static public function
	PathableKey(mixed $Input):
	string {

		static::Prepare($Input);

		if(!is_string($Input))
		$Input = (string)$Input;

		// trim up and force to lower case.

		$Output = strtolower(trim($Input));

		// strip out anything not ultra safe.
		// A-Z, 0-9, Dash, FSlash, Dot.

		$Output = str_replace(' ', '-', $Output);
		$Output = preg_replace('#[^a-zA-Z0-9\-\/\.]#', '', $Output);

		// traversal foolery: collapse dots.

		$Output = preg_replace('#[\.]{2,}#', '.', $Output);

		// eyeball foolery: collapse dashes, slashes, dashyslashes.

		$Output = preg_replace('#[\-]{2,}#', '-', $Output);
		$Output = preg_replace('#(?:\.*/-*)|(?:-*/-*)#', '/', $Output);
		$Output = preg_replace('#(?:[/]{2,})#', '/', $Output);

		return $Output;
	}

	#[Common\Meta\DateAdded('2022-02-17')]
	#[Common\Meta\Info('Make a key slottable instead of pathable by stripping out any forward slashes.')]
	static public function
	SlottableKey(mixed $Input):
	string {

		$Output = static::PathableKey($Input);
		$Output = str_replace('/', '-', $Output);

		return $Output;
	}

	#[Common\Meta\DateAdded('2022-02-17')]
	#[Common\Meta\Info('Generate a Pascal formatted string from a Key formatted string.')]
	static public function
	PascalFromKey(mixed $Input):
	string {

		static::Prepare($Input);

		$Output = preg_replace('#([A-Z])#', ' \\1', $Input);
		$Output = static::SlottableKey($Output);
		$Output = ucwords(str_replace('-', ' ', $Output));
		$Output = preg_replace('#[^A-Za-z0-9]#', '', $Output);

		return $Output;
	}

	#[Common\Meta\DateAdded('2023-07-06')]
	#[Common\Meta\Info('Convert spaced indenting into tabbed indenting.')]
	static public function
	Tabbify(mixed $Val, int $Spaces=4):
	string {

		static::Prepare($Val);

		if(!is_string($Val))
		$Val = (string)$Val;

		$Val = preg_replace_callback(
			'#^ {1,}#ms',
			fn($Matches)
			=> str_repeat("\t", (int)(strlen($Matches[0]) / $Spaces)),
			$Val
		);

		return $Val;
	}

}
