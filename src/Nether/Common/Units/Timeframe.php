<?php

namespace Nether\Common\Units;

use DateTime;
use Stringable;

class Timeframe
implements Stringable {
/*//
@date 2023-02-03
Provides means of getting how long it has been between two points in time with
some built in formatting, and handling of how specific you really want to be.

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

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(mixed $Start=NULL, mixed $Stop=NULL, ?array $Format=NULL, ?string $Join=NULL, ?int $Precision=NULL) {

		$Now = time();

		($this)
		->SetStart($Start)
		->SetStop($Stop)
		->SetFormat($Format)
		->SetJoin($Join)
		->SetPrecision($Precision);

		return;
	}

	public function
	__Invoke(mixed $Start=NULL, mixed $Stop=NULL, ?array $Format=NULL, ?string $Join=NULL, ?int $Precision=NULL):
	string {

		if($Start || $Stop) {
			($this)
			->SetStart($Start)
			->SetStop($Stop);
		}

		return $this->Get(
			Format: $Format,
			Join: $Join,
			Precision: $Precision
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
	Get(?array $Format=NULL, ?string $Join=NULL, ?int $Precision=NULL):
	string {

		$Format ??= $this->Format;
		$Join ??= $this->Join;
		$Precision ??= $this->Precision;

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

		return trim(join($Join, $Dataset));
	}

	public function
	SetStart(mixed $When):
	static {

		$When = $this->HandleTimeInput($When);

		$this->Start = new DateTime($When);

		return $this;
	}

	public function
	SetStop(mixed $When):
	static {

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

}

