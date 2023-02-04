<?php

namespace Nether\Common\Units;

use DateTime;
use Stringable;

class Timeframe
implements Stringable {

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

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(mixed $Start=NULL, mixed $Stop=NULL) {

		$Now = time();

		($this)
		->SetStart($Start)
		->SetStop($Stop);

		return;
	}

	public function
	__Invoke(mixed $Start=NULL, mixed $Stop=NULL, ?array $Format=NULL, ?string $Join=NULL):
	string {

		($this)
		->SetStart($Start)
		->SetStop($Stop);

		return $this->Get(
			Format: $Format,
			Join: $Join
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
	SetStart(mixed $When):
	static {

		$When = match(TRUE) {
			(is_int($When))=> "@{$When}",
			($When === NULL)=> 'now',
			default=> $When
		};

		$this->Start = new DateTime($When);

		return $this;
	}

	public function
	SetStop(mixed $When):
	static {

		$When = match(TRUE) {
			(is_int($When))=> "@{$When}",
			($When === NULL)=> 'now',
			default=> $When
		};

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

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Span(mixed $Start, mixed $Stop):
	static {

		($this)
		->SetStart($Start)
		->SetStop($Stop);

		return $this;
	}

	public function
	Get(?array $Format=NULL, ?string $Join=NULL):
	string {

		$Format ??= $this->Format;
		$Join ??= $this->Join;

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

		return trim(join($Join, $Dataset));
	}

	public function
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

