<?php

namespace Nether\Common\Units;

use Nether\Common;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Stringable;

class Timeframe
implements Stringable {
/*//
@date 2023-02-03
Provides means of getting how long it has been between two points in time with
some built in formatting, and handling of how specific you really want to be.
Note we primarily work with unix time integers, so if you start passing strings
in you will need to begin to take responsiblity for formats and timezones.

## Typical Usage:

Providing a Start time without a Stop time assumes it should compare against
right now. Same as if provided a Stop time without an end time. The start and
stop times can be any format that PHP's strtotime/DateInterval can consume.

	$Time = strtotime('+ 1 day 3 hour 5min 42sec');

	$Since = new Timeframe($Time);
	echo $Since->Get(); // 1d 3hr 5min 42sec

	$Since = new Timeframe(Stop: $Time);
	echo $Since->Get(); // - 1d 3hr 5min 42sec

Formatting can be changed instancewide or for a single invocation. Using the
setters will update the instance, using invocation args will only apply to that
one call.

	echo $Since->SetFormat($Since::FormatLong)->Get();
	echo $Since->Get(Format: $Since::FormatLong);
	// 1 day 3 hours 5 minutes 42 seconds

	echo $Since->SetJoin(', )->Get();
	echo $Since->Get(Join: ', ');
	// 1 day, 3 hours, 5 minutes, 42 seconds

Printing via Invokable will print using all the current instance settings and
can take the same arguments as the main Get() method with the addition of a
Start and Stop argument. Providing either a Start or Stop will trigger the same
rules as when providing either a Start or Stop to the constructor.

	echo $Since(1, 2); // 1 second
	echo $Since(Stop: '+1 second'); // 1 second
	echo $Since(Start: '+1 second'); // -1 second
	echo $Since(Format: $Since::FormatShort); // - 1s

Printing via Stringable will print using all the current instance settings.

	echo $Since; // -1 second

//*/

	const
	FormatLong = [
		'sign' => '%r',
		'y' => '%y [year|years]',
		'm' => '%m [month|months]',
		'd' => '%d [day|days]',
		'h' => '%h [hour|hours]',
		'i' => '%i [minute|minutes]',
		's' => '%s [second|seconds]'
	];

	const
	FormatDefault = [
		'sign' => '%r',
		'y' => '%yyr',
		'm' => '%mmo',
		'd' => '%dd',
		'h' => '%hhr',
		'i' => '%imin',
		's' => '%ssec'
	];

	const
	FormatShort = [
		'sign' => '%r',
		'y' => '%yyr',
		'm' => '%mmo',
		'd' => '%dd',
		'h' => '%hh',
		'i' => '%im',
		's' => '%ss'
	];

	const
	FormatShorter = [
		'sign' => '%r',
		'y' => '%yy',
		'm' => '%mm',
		'd' => '%dd',
		'h' => '%hh',
		'i' => '%im',
		's' => '%ss'
	];

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected DateTime
	$Start;

	protected DateTime
	$Stop;

	protected bool
	$SkipZero = TRUE;

	protected array
	$Format = self::FormatDefault;

	protected string
	$Join = ' ';

	protected ?int
	$Precision = NULL;

	protected ?string
	$EmptyString = NULL;

	public int
	$EmptyDiff = 0;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(mixed $Start=NULL, mixed $Stop=NULL, ?array $Format=NULL, ?string $Join=NULL, ?int $Precision=NULL, ?string $EmptyString=NULL, int $EmptyDiff=0) {

		$Now = time();

		($this)
		->SetStart($Start)
		->SetStop($Stop)
		->SetFormat($Format)
		->SetJoin($Join)
		->SetPrecision($Precision)
		->SetEmptyString($EmptyString)
		->SetEmptyDiff($EmptyDiff);

		return;
	}

	public function
	__Invoke(mixed $Start=NULL, mixed $Stop=NULL, ?array $Format=NULL, ?string $Join=NULL, ?int $Precision=NULL, ?string $EmptyString=NULL, ?int $EmptyDiff=NULL):
	string {

		// @todo 2023-03-17
		// consider pulling start and stop out of this invoke style call
		// since they are not temporary overrides to the object like all
		// the other args are. that or consider making them passable to
		// the get method as temporary overrides as well. case for that
		// being you have a single start date but want to roll against a
		// series of various end dates.

		if($Start || $Stop) {
			($this)
			->SetStart($Start)
			->SetStop($Stop);
		}

		return $this->Get(
			Format: $Format,
			Join: $Join,
			Precision: $Precision,
			EmptyString: $EmptyString,
			EmptyDiff: $EmptyDiff
		);
	}

	public function
	__ToString():
	string {

		return $this->Get();
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Get(?array $Format=NULL, ?string $Join=NULL, ?int $Precision=NULL, ?string $EmptyString=NULL, ?int $EmptyDiff=NULL):
	string {

		$Format ??= $this->Format;
		$Join ??= $this->Join;
		$Precision ??= $this->Precision;
		$EmptyString ??= $this->EmptyString;
		$EmptyDiff ??= $this->EmptyDiff;

		$Diff = $this->Start->Diff($this->Stop);
		$Key = NULL;
		$Fmt = NULL;
		$Dataset = [];

		foreach($Format as $Key => $Fmt) {
			if(strlen($Key) === 1)
			if(property_exists($Diff, $Key)) {
				if($this->SkipZero && $Diff->{$Key} === 0)
				continue;

				$Fmt = $this->ParseFormat($Fmt, $Key, $Diff->{$Key});
			}

			$Dataset[] = $Diff->Format($Fmt);
		}

		$Dataset = array_filter(
			$Dataset,
			fn(string $Data)=> !!trim($Data)
		);

		if($Precision !== NULL)
		$Dataset = array_slice($Dataset, 0, $Precision);

		////////

		if($EmptyString)
		if($this->IntervalToSeconds($Diff) <= $EmptyDiff)
		return $EmptyString;

		////////

		return trim(join($Join, $Dataset));
	}

	public function
	GetTimeDiff():
	int {

		$TimeStart = $this->GetStartTime();
		$TimeStop = $this->GetStopTime();

		return ($TimeStop - $TimeStart);
	}

	public function
	GetStart():
	DateTime {

		return $this->Start;
	}

	public function
	GetStartTime():
	int {

		return $this->Start->GetTimestamp();
	}

	public function
	GetStartFormat(string $Format):
	string {

		return $this->Start->Format($Format);
	}

	public function
	GetStop():
	DateTime {

		return $this->Stop;
	}

	public function
	GetStopTime():
	int {

		return $this->Stop->GetTimestamp();
	}

	public function
	GetStopFormat(string $Format):
	string {

		return $this->Stop->Format($Format);
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	SetStart(mixed $When):
	static {

		if(is_float($When))
		$When = (int)round($When, 0);

		////////

		$When = $this->HandleTimeInput($When);

		$this->Start = new DateTime($When);

		return $this;
	}

	public function
	SetStop(mixed $When):
	static {

		if(is_float($When))
		$When = (int)round($When, 0);

		////////

		$When = $this->HandleTimeInput($When);

		$this->Stop = new DateTime($When);

		return $this;
	}

	public function
	SetSkipZero(bool $Skip):
	static {

		$this->SkipZero = $Skip;

		return $this;
	}

	public function
	SetFormat(?array $Format=NULL):
	static {

		$this->Format = $Format ?? static::FormatDefault;

		return $this;
	}

	public function
	SetJoin(?string $Join=NULL):
	static {

		$this->Join = $Join ?? ' ';

		return $this;
	}

	public function
	SetPrecision(?int $Prec=NULL):
	static {

		$this->Precision = $Prec;

		return $this;
	}

	public function
	SetEmptyDiff(int $Diff):
	string {

		$this->EmptyDiff = $Diff;
		return $this;
	}

	public function
	SetEmptyString(?string $Str=NULL):
	static {

		$this->EmptyString = $Str;
		return $this;
	}

	public function
	Span(mixed $Start, mixed $Stop):
	static {

		($this)
		->SetStart($Start)
		->SetStop($Stop);

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected function
	HandleTimeInput(mixed $When):
	mixed {

		return match(TRUE) {
			(is_int($When))
			=> sprintf('@%d', $When),

			($When === NULL)
			=> sprintf('@%d', time()),

			($When instanceof Common\Date)
			=> $When->Get(Common\Values::DateFormatYMDT24VZ),

			default
			=> $When
		};
	}

	protected function
	ParseFormat(string $Fmt, string $Key, int $Val):
	string {

		$RegSingPlur = '#\[(.+?)\|(.+?)\]#';

		if(preg_match($RegSingPlur, $Fmt)) {
			if($Val === 1)
			$Fmt = preg_replace($RegSingPlur, '$1', $Fmt);
			else
			$Fmt = preg_replace($RegSingPlur, '$2', $Fmt);
		}

		return $Fmt;
	}

	protected function
	IntervalToSeconds(DateInterval $Diff):
	int {

		$DT = new DateTime('@0');
		$DT->Add($Diff);

		return (int)abs($DT->GetTimestamp());
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	Today(string $Timezone='GMT'):
	static {

		$DateTimeZone = new DateTimeZone($Timezone);
		$Today = new DateTimeImmutable('today', $DateTimeZone);
		$Tomorrow = $Today->Modify('+1 day')->Modify('-1 second');

		$Start = $Today->Format(Common\Values::DateFormatYMDT24VO);
		$Stop = $Tomorrow->Format(Common\Values::DateFormatYMDT24VO);

		$Output = new static(
			Start: $Start,
			Stop: $Stop
		);

		return $Output;
	}

	static public function
	FromDecade(int $Year, bool $EndOn=TRUE):
	static {

		$Start = sprintf('%d-01-01 00:00:00 GMT', $Year);

		$Stop = match($EndOn) {
			TRUE  => sprintf('%d-12-31 23:59:59 GMT', ($Year + 9)),
			FALSE => sprintf('%d-01-01 00:00:00 GMT', ($Year + 10)),
		};

		$Output = new static(
			Start: $Start,
			Stop: $Stop
		);

		return $Output;
	}

	static public function
	FromDecadeInner(int $Year):
	static {

		return static::FromDecade($Year, TRUE);
	}

	static public function
	FromDecadeOuter(int $Year):
	static {

		return static::FromDecade($Year, FALSE);
	}

}

