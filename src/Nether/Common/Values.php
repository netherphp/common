<?php

namespace Nether\Common;

class Values {

	const
	NullUUID = '00000000-0000-0000-0000-000000000000';

	const
	Zero = 0,
	One  = 1;

	const
	SecPerMin = 60,
	SecPerHr  = 3600,
	SecPerDay = 86400,
	MinPerHr  = 60,
	MinPerDay = 1440,
	HrPerDay  = 24,
	DayPerWk  = 7;

	const
	BitsPerByte = 8,
	BitsPerUnit = 1024;

	const
	ByteMax = 0b11111111;

	const
	CircleDegMax = 360,
	CircleRadMax = (M_PI * 2);

	////////

	const
	DateFormatYMD              = 'Y-m-d',
	DateFormatYMDT12           = 'Y-m-d g:ia',
	DateFormatYMDT12Z          = 'Y-m-d g:ia T',
	DateFormatYMDT12O          = 'Y-m-d g:ia O',
	DateFormatYMDT12V          = 'Y-m-d g:i:sa',
	DateFormatYMDT12VZ         = 'Y-m-d g:i:sa T',
	DateFormatYMDT12VO         = 'Y-m-d g:i:sa O',
	DateFormatYMDT24           = 'Y-m-d H:i',
	DateFormatYMDT24Z          = 'Y-m-d H:i T',
	DateFormatYMDT24O          = 'Y-m-d H:i O',
	DateFormatYMDT24V          = 'Y-m-d H:i:s',
	DateFormatYMDT24VZ         = 'Y-m-d H:i:s T',
	DateFormatYMDT24VO         = 'Y-m-d H:i:s O',
	DateFormatBasicDate        = 'M j Y',
	DateFormatFancyDateTime    = 'F jS Y g:ia',
	DateFormatFancyDate        = 'F jS, Y',
	DateFormatFancyDateVerbose = 'l F jS, Y',
	DateFormatT24              = 'H:i',
	DateFormatT24Z             = 'H:i T',
	DateFormatT24O             = 'H:i O',
	DateFormatT24V             = 'H:i:s',
	DateFormatT24VZ            = 'H:i:s T',
	DateFormatT24VO            = 'H:i:s O',
	DateFormatT12              = 'g:ia',
	DateFormatT12Z             = 'g:ia T',
	DateFormatT12O             = 'g:ia O',
	DateFormatTZ               = 'T',
	DateFormatTO               = 'O',
	DateFormatUnix             = 'U';

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	DebugProtectValue(mixed $Val):
	string {

		$Type = gettype($Val);
		$Out = match($Type) {
			'string'
			=> sprintf(
				'[protected %s len:%d]',
				$Type, strlen($Val)
			),

			default
			=> sprintf('[protected %s]', $Type)
		};

		return $Out;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Meta\Date('2024-04-15')]
	#[Meta\Info('Returns $One if $Num is 1, otherwise returns $Other.')]
	static public function
	IfOneElse(int $Num, mixed $One, mixed $Other):
	mixed {

		return $Num === 1 ? $One : $Other;
	}

	#[Meta\Date('2024-04-15')]
	#[Meta\Info('Returns $One if $Truth, otherwise returns $Other.')]
	static public function
	IfTrueElse(bool $Test, mixed $One, mixed $Other):
	mixed {

		return $Test ? $One : $Other;
	}

	#[Meta\Date('2024-11-21')]
	static public function
	IsNth(int $Num, int $Nth):
	bool {

		return (($Num % $Nth) === 0);
	}

	#[Meta\Date('2024-11-21')]
	static public function
	IsEven(int $Num):
	bool {

		return static::IsNth($Num, 2);
	}

	#[Meta\Date('2024-11-21')]
	static public function
	IsOdd(int $Num):
	bool {

		return !static::IsNth($Num, 2);
	}

	#[Meta\Date('2024-04-29')]
	#[Meta\Info('Check if a string is only made up of numbers (Base 10).')]
	static public function
	IsNumericDec(mixed $Input):
	bool {

		// just do not trust is_numeric() and the stupid amount of things
		// it checks when all we wanted was a series of decimal digits in
		// a string.

		if(is_int($Input) || is_float($Input))
		return TRUE;

		if(!is_string($Input))
		return FALSE;

		$Input ??= '';
		$Num = preg_match('/^[0-9\.]{1,}$/', $Input);

		return (TRUE
			&& $Num !== FALSE
			&& $Num > 0
		);
	}

	#[Meta\Date('2024-04-29')]
	#[Meta\Info('Check if a string is only made up of numbers (Base 16).')]
	static public function
	IsNumericHex(?string $Input):
	bool {

		$Input ??= '';
		$Num = preg_match('/^[0-9a-fA-F]{1,}$/', $Input);

		return (TRUE
			&& $Num !== FALSE
			&& $Num > 0
		);
	}

	#[Meta\Date('2025-07-31')]
	#[Meta\Info('When people backspace the HTML Editor empty it often leaves behind an empty div>br that breaks more simple empty checks.')]
	static public function
	IsEmptyEditorString(?string $Input):
	bool {

		// the simple check.

		if(!$Input)
		return TRUE;

		// check if it was empty html.

		if(strlen($Input) < 32)
		if(strip_tags($Input) === '')
		return TRUE;

		////////

		return FALSE;
	}

	#[Meta\Date('2025-08-08')]
	#[Meta\Info('I am beyond infurated that we do not have an !instanceof operator.')]
	static public function
	IsInstanceOf(mixed $Thing, string $Class):
	bool {

		return ($Thing instanceof $Class);
	}

	#[Meta\Date('2025-07-07')]
	static public function
	IsOnWindows():
	bool {

		return (PHP_OS_FAMILY === 'Windows');
	}

	#[Meta\Date('2025-07-07')]
	static public function
	IsOnUnix():
	bool {

		return (PHP_OS_FAMILY !== 'Windows');
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Meta\Date('2024-06-26')]
	#[Meta\Info('Convert a key value map into a list of key=value params.')]
	static public function
	MapToParams(string $K, mixed $V):
	string {

		return sprintf('%s="%s"', $K, $V);
	}

}
