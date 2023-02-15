<?php

namespace Nether\Common;

class Values {

	const
	SecPerMin = 60,
	SecPerHr  = 3600,
	SecPerDay = 86400;

	const
	DateFormatYMD              = 'Y-m-d',
	DateFormatYMDT12           = 'Y-m-d g:ia',
	DateFormatYMDT12Z          = 'Y-m-d g:ia T',
	DateFormatYMDT12V          = 'Y-m-d g:i:sa',
	DateFormatYMDT12VZ         = 'Y-m-d g:i:sa T',
	DateFormatYMDT24           = 'Y-m-d H:i',
	DateFormatYMDT24Z          = 'Y-m-d H:i T',
	DateFormatYMDT24V          = 'Y-m-d H:i:s',
	DateFormatYMDT24VZ         = 'Y-m-d H:i:s T',
	DateFormatFancyDateTime    = 'F jS Y g:ia',
	DateFormatFancyDate        = 'F jS, Y',
	DateFormatFancyDateVerbose = 'l F jS, Y',
	DateFormatT24              = 'H:i',
	DateFormatT24Z             = 'H:i T',
	DateFormatT12              = 'g:ia',
	DateFormatT12Z             = 'g:ia T',
	DateFormatTZ               = 'T',
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

}
