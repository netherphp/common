<?php

namespace Nether\Common\Units;

use DateTime;

class Timeframe {

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
	FormatNormal = [
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

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(mixed $Start=NULL, mixed $Stop=NULL) {

		$Now = time();

		($this)
		->SetStart($Start ?? "@{$Now}")
		->SetStop($Stop ?? "@{$Now}");

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	SetStart(mixed $Start):
	static {

		if(is_int($Start))
		$Start = "@{$Start}";

		$this->Start = new DateTime($Start);

		return $this;
	}

	public function
	SetStop(mixed $Stop):
	static {

		if(is_int($Stop))
		$Stop = "@{$Stop}";

		$this->Stop = new DateTime($Stop);

		return $this;
	}

	public function
	SetSkipZero(bool $Skip):
	static {

		$this->SkipZero = $Skip;

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Set(mixed $Start, mixed $Stop):
	static {

		($this)
		->SetStart($Start)
		->SetStop($Stop);

		return $this;
	}

	public function
	Get(array $Format=self::FormatNormal):
	string {

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

		return trim(join(' ', $Dataset));
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

