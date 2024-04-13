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

	static public function
	IfOneElse(int $Num, mixed $One, mixed $Other):
	mixed {

		return $Num === 1 ? $One : $Other;
	}

}
