<?php

namespace Nether\Common;

use Nether\Atlantis;
use Nether\Common;

use DateTime;
use DateTimeZone;
use Stringable;
use JsonSerializable;

class Date
implements
	Stringable,
	JsonSerializable {

	const
	ConfDefaultTimezone = 'Nether.Common.Date.Timezone';

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected DateTime
	$DateTime;

	protected string
	$DateFormat = Common\Values::DateFormatYMD;

	protected string
	$TimeFormat = Common\Values::DateFormatT12Z;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(mixed $Input='now') {

		$this->DateTime = new DateTime($Input);

		$this->DateTime->SetTimezone(new DateTimeZone(
			Common\Library::Get(static::ConfDefaultTimezone)
			?? 'UTC'
		));

		return;
	}

	public function
	__ToString():
	string {

		return $this->DateTime->Format($this->DateFormat);
	}

	public function
	__Invoke(...$Argv):
	?string {

		if(count($Argv) === 1)
		return $this->DateTime->Format($Argv[0]);

		return NULL;
	}

	public function
	JsonSerialize():
	array {

		return [
			'DateTime' => $this->DateTime->Format(DateTime::RFC3339),
			'Unix'     => $this->DateTime->Format(Common\Values::DateFormatUnix),
			'Date'     => $this->DateTime->Format($this->DateFormat),
			'Time'     => $this->DateTime->Format($this->TimeFormat)
		];
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Get(?string $Format=NULL):
	string {

		$Format = $Format ?? $this->DateFormat;

		return $this->DateTime->Format($Format);
	}

	public function
	GetDateFormat():
	string {

		return $this->DateFormat;
	}

	public function
	SetDateFormat(string $Format):
	static {

		$this->DateFormat = $Format;
		return $this;
	}

	public function
	GetTimeFormat():
	string {

		return $this->TimeFormat;
	}

	public function
	SetTimeFormat(string $Format):
	static {

		$this->TimeFormat = $Format;
		return $this;
	}

	public function
	GetUnixtime():
	int {

		return (int)$this->DateTime->Format(
			Common\Values::DateFormatUnix
		);
	}

	public function
	SetTimezone(mixed $TZ):
	static {

		$this->DateTime->SetTimezone(
			new DateTimeZone($TZ)
		);

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	FromDateString(string $Date, ?string $Timezone=NULL):
	static {
	/*//
	@date 2022-05-04
	consider the timezone when making an object from a date string. if a user
	passes in 2010-01-01 they are likely expecting it to be in their (or the
	site's default) timezone. without considering that might get off by one
	day type deals when rehydrating this from the unix timestamp later.
	//*/

		if($Timezone === NULL)
		$Timezone = (
			Common\Library::Get('Nether.Common.Date.Timezone')
			?? 'UTC'
		);

		return new static("{$Date} {$Timezone}");
	}

	static public function
	FromTime(mixed $Time):
	static {
	/*//
	@date 2021-08-26
	//*/

		return new static("@{$Time}");
	}

}
