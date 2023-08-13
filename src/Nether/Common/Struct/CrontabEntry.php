<?php

namespace Nether\Common\Struct;

use Nether\Common;

use Exception;
use Stringable;

////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////

class CrontabEntry
implements Stringable {

	const
	FmtCrontabRegex = '/^([^ ]+)[\h\s]+([^ ]+)[\h\s]+([^ ]+)[\h\s]+([^ ]+)[\h\s]+([^ ]+)[\h\s]+(.+?)$/';

	const
	Sun = 0,
	Mon = 1,
	Tue = 2,
	Wed = 3,
	Thu = 4,
	Fri = 5,
	Sat = 6;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public int|string|NULL
	$Minute = NULL;

	public int|string|NULL
	$Hour = NULL;

	public int|string|NULL
	$Day = NULL;

	public int|string|NULL
	$Month = NULL;

	public int|string|NULL
	$Weekday = NULL;

	public ?string
	$Command = NULL;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__ToString():
	string {

		return $this->GetAsCrontab();
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	GetAsCrontab():
	string {

		if(!$this->Command)
		throw new Exception('no Command set');

		return sprintf(
			'%s %s %s %s %s %s',
			static::CrontabValue($this->Minute),
			static::CrontabValue($this->Hour),
			static::CrontabValue($this->Day),
			static::CrontabValue($this->Month),
			static::CrontabValue($this->Weekday),
			($this->Command ?? '')
		);
	}

	public function
	GetTimerAsWords():
	string {

		$Date = $this->GetTimerAsObject();

		return $Date->Get(Common\Values::DateFormatYMDT24VZ);
	}

	public function
	GetTimerAsObject():
	Common\Date {

		$Now = new Common\Date;
		$Output = NULL;
		$UseLocal = TRUE;

		$Min = $this->Minute ?? $Now->Get('i');
		$Hour = $this->Hour ?? $Now->Get('H');
		$Day = $this->Day ?? $Now->Get('d');
		$Month = $this->Month ?? $Now->Get('m');
		$Year = $Now->Get('Y');
		$TZ = Common\Date::FetchTimezoneFromSystem();

		$Output = new Common\Date(sprintf(
			"%s-%s-%s %s:%s %s",
			$Year, $Month, $Day,
			$Hour, $Min, $TZ
		));

		// if the job has not happened yet then we are done, this is the
		// time we want to report.

		if($Now->IsAfter($Output))
		return $Output;

		// time units which are wildcarded will be bumped until a launch
		// time is finally in the future.

		if($this->Minute === NULL) {
			$Output->Modify('+1 minute');

			if($Now->IsAfter($Output))
			return $Output;
		}

		if($this->Hour === NULL) {
			$Output->Modify('+1 hour');

			if($Now->IsAfter($Output))
			return $Output;
		}

		if($this->Day === NULL) {
			$Output->Modify('+1 day');

			if($Now->IsAfter($Output))
			return $Output;
		}

		if($this->Month === NULL) {
			$Output->Modify('+1 month');

			if($Now->IsAfter($Output))
			return $Output;
		}

		return $Output;
	}

	public function
	GetTimerAsInt():
	int {

		$Date = $this->GetTimerAsObject();

		return $Date->GetUnixtime();
	}

	public function
	GetTimerAsTimeframe():
	Common\Units\Timeframe {

		$Start = $this->GetTimerAsInt();
		$Now = Common\Date::CurrentUnixtime();

		return new Common\Units\Timeframe(
			$Now,
			$Start,
			Precision: 2
		);
	}

	public function
	GetWordsForHours():
	string {

		return sprintf('%02s:%02s', $this->Hour, $this->Minute);
	}

	public function
	IsDaily():
	bool {

		if($this->Day !== NULL || $this->Month !== NULL || $this->Weekday !== NULL)
		return FALSE;

		if(!is_numeric($this->Hour))
		return FALSE;

		////////

		return TRUE;
	}

	public function
	IsHourly():
	bool {

		if($this->Day !== NULL || $this->Month !== NULL || $this->Weekday !== NULL)
		return FALSE;

		if($this->Hour !== NULL || $this->Minute === NULL)
		return FALSE;

		////////

		return TRUE;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	SetCommand(string $Cmd):
	static {

		$this->Command = $Cmd;

		return $this;
	}

	public function
	SetDayOfMonth(int|string|NULL $Day=NULL):
	static {
	/*//
	@date 2023-05-30
	//*/

		$this->Day = $Day;

		return $this;
	}

	public function
	SetDayOfWeek(int|string|NULL $Weekday=NULL):
	static {
	/*//
	@date 2023-05-30
	//*/

		$this->Weekday = $Weekday;

		return $this;
	}

	public function
	SetMonth(int|string|NULL $Month=NULL):
	static {
	/*//
	@date 2023-05-30
	//*/

		$this->Month = $Month;

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	SetupDailyTime(int|string $Time):
	static {
	/*//
	@date 2023-05-30
	configure to run daily at the specified time in 24hr fmt.
	//*/

		// if given something resembling military time then expect to be
		// able to parse and pad against HHMM.

		if(is_int($Time) || !str_contains($Time, ':')) {
			$Time = sprintf('%04s', $Time);

			$this->SetupDailyValues(
				substr($Time, 0, 2) ?: NULL,
				substr($Time, 2, 2) ?: NULL
			);

			return $this;
		}

		// if given something that resembles pleb time then expect to be
		// be able to parse it against g:ia.

		$Time = explode(':', $Time);

		// bump the hour if user specified pm.

		if(str_ends_with(strtolower($Time[1]), 'pm') && $Time[0] <= 12)
		$Time[0] = (int)$Time[0] + 12;

		$this->SetupDailyValues(
			(int)$Time[0],
			(int)$Time[1]
		);

		return $this;
	}

	public function
	SetupDailyValues(int|string|NULL $Hour=NULL, int|string|NULL $Minute=NULL):
	static {
	/*//
	@date 2023-05-30
	configure to run daily at the specified hour and second.
	//*/

		$this->ResetTimer();
		$this->Hour = $Hour;
		$this->Minute = $Minute;

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Reset():
	static {

		$this->Command = NULL;
		$this->ResetTimer();

		return $this;
	}

	public function
	ResetTimer():
	static {

		$this->Minute = NULL;
		$this->Hour = NULL;
		$this->Day = NULL;
		$this->Month = NULL;
		$this->Weekday = NULL;

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	CrontabValue(?string $Input):
	?string {

		if($Input === NULL || $Input === '*')
		return '*';

		if(is_numeric($Input))
		return (string)(int)$Input;

		return $Input;
	}

	static public function
	FromCrontab(string $Line):
	?static {

		$Found = NULL;
		$Data = NULL;

		////////

		preg_match(
			static::FmtCrontabRegex,
			$Line, $Found
		);

		if(count($Found) !== 7)
		return NULL;

		////////

		$Data = (
			(new Common\Datastore(array_slice($Found, 1, 6)))
			->Remap(fn(string $Val)=> trim($Val))
			->Remap(fn(string $Val)=> $Val === '*' ? NULL : $Val)
		);

		////////

		return static::FromValues(...$Data->GetData());
	}

	static public function
	FromValues(int|string|NULL $Minute=NULL, int|string|NULL $Hour=NULL, int|string|NULL $Day=NULL, int|string|NULL $Month=NULL, int|string|NULL $Weekday=NULL, ?string $Command=NULL):
	static {

		$Output = new static;
		$Output->Minute = $Minute;
		$Output->Hour = $Hour;
		$Output->Day = $Day;
		$Output->Month = $Month;
		$Output->Weekday = $Weekday;
		$Output->Command = $Command;

		return $Output;
	}

}

