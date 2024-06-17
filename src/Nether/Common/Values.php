<?php

namespace Nether\Common;

class Values {

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

	#[Meta\Date('2024-04-29')]
	#[Meta\Info('Check if a string is only made up of numbers (Base 10).')]
	static public function
	IsNumericDec(?string $Input):
	bool {

		// just do not trust is_numeric() and the stupid amount of things
		// it checks when all we wanted was a series of decimal digits in
		// a string.

		$Input ??= '';
		$Num = preg_match('/^[0-9]{1,}$/', $Input);

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

}
