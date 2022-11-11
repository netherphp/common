<?php

namespace Nether\Common\Units;
use Nether;

class Timeframe {
/*//
@date 2022-11-10
given two times in seconds (int|float) or a string that can be converted
into a time by strtotime, calculate the timespan between the two points. when
zeroing the time difference is forced to be zero when it rolls past zero. if
leaping is enabled then the math will use a fractional days-per-year.
//*/

	protected mixed
	$Start;

	protected mixed
	$Stop;

	protected bool
	$Leap = TRUE;

	protected bool
	$Zero = FALSE;

	protected string
	$UnitSep = ', ';

	protected string
	$LabelSet = 'Full';

	protected string
	$LabelSep = ' ';

	protected int
	$LabelCase = 0;

	protected array
	$LabelSets = [
		'Full'     => [ 'Y'=> 'Years', 'D'=> 'Days', 'H'=> 'Hours', 'M'=> 'Minutes', 'S'=> 'Seconds' ],
		'Short'    => [ 'Y'=> 'Yr', 'D'=> 'D', 'H'=> 'Hr', 'M'=> 'Min', 'S'=> 'Sec' ],
		'Shortest' => [ 'Y'=> 'Y', 'D'=> 'D', 'H'=> 'H', 'M'=> 'M', 'S'=> 'S' ]
	];

	public function
	__Construct(
		int|float|string|NULL $Start = NULL,
		int|float|string|NULL $Stop  = NULL
	) {

		$this->SetStart($Start);
		$this->SetStop($Stop);

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	SetStart(int|float|string|NULL $Time=NULL):
	static {

		if($Time === NULL)
		$Time = microtime(TRUE);

		elseif(is_string($Time))
		$Time = strtotime($Time);

		////////

		$this->Start = $Time;
		return $this;
	}

	public function
	SetStop(?int $Time=NULL):
	static {

		if($Time === NULL)
		$Time = microtime(TRUE);

		elseif(is_string($Time))
		$Time = strtotime($Time);

		////////

		$this->Stop = $Time;
		return $this;
	}

	public function
	SetUnitSep(?string $What=NULL):
	static {

		$this->UnitSep = $What ?? ', ';
		return $this;
	}

	public function
	SetLabelCase(?int $How=0):
	static {

		if($How === NULL)
		$this->LabelCase = 0;

		else
		$this->LabelCase = ($How <=> 0);

		return $this;
	}

	public function
	SetLabelSep(?string $What=NULL):
	static {

		$this->LabelSep = $What ?? ' ';
		return $this;
	}

	public function
	SetLabelSet(?string $Which=NULL):
	static {

		if(!$Which)
		$Which = 'Full';

		if(array_key_exists($Which, $this->LabelSets))
		$this->LabelSet = $Which;

		return $this;
	}

	public function
	SetZeroing(bool $Should):
	static {

		$this->Zero = $Should;
		return $this;
	}

	public function
	SetLeaping(bool $Should):
	static {

		$this->Leap = $Should;
		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Calc():
	array {

		$SecPerMin = 60;
		$MinPerHr = 60;
		$HrPerDay = 24;
		$DayPerYr = $this->Leap ? 365.25 : 365;

		$Diff = NULL;
		$Seconds = NULL;
		$Minutes = NULL;
		$Hours = NULL;
		$Days = NULL;
		$Years = NULL;
		$Dir = 1;

		////////


		$Diff = abs($this->Stop - $this->Start);

		if($this->Stop < $this->Start)
		$Dir = -1;

		if($this->Zero) {
			if($Dir === 1 && $Diff < 0)
			$Diff = 0;

			elseif($Dir === -1 && $Diff > 0)
			$Diff = 0;
		}

		////////

		$Seconds = floor(
			($Diff) % $SecPerMin
		);

		$Minutes = floor(
			($Diff / $SecPerMin) % $MinPerHr
		);

		$Hours = floor(
			($Diff / ($MinPerHr * $SecPerMin)) % $HrPerDay
		);

		$Days = floor(
			($Diff / ($HrPerDay * $MinPerHr * $SecPerMin)) % $DayPerYr
		);

		$Years = floor(
			($Diff / ($HrPerDay * $MinPerHr * $SecPerMin * $DayPerYr))
		);

		return [
			'Dir'   => $Dir,
			'Total' => $Diff,
			'Y'     => $Years,
			'D'     => $Days,
			'H'     => $Hours,
			'M'     => $Minutes,
			'S'     => $Seconds
		];
	}

	public function
	GetLabelSet():
	array {

		$LabelSet = $this->LabelSets[$this->LabelSet];

		if($this->LabelCase === 1) {
			foreach($LabelSet as &$Label)
			$Label = strtoupper($Label);
		}

		elseif($this->LabelCase === -1) {
			foreach($LabelSet as &$Label)
			$Label = strtolower($Label);
		}

		return $LabelSet;
	}

	public function
	GetDiffString():
	string {

		$Calc = $this->Calc();
		$LabelSet = $this->GetLabelSet();
		$Key = NULL;

		// run through the units.

		foreach(['Y', 'D', 'H', 'M', 'S'] as $Key)
		if($Calc[$Key] !== 0)
		$Output[] = "{$Calc[$Key]}{$this->LabelSep}{$LabelSet[$Key]}";

		// compile the final string.

		$String = (($Calc['Dir'] > 0) ? '+ ' : '- ');
		$String .= join($this->UnitSep, $Output);

		return $String;
	}

	public function
	GetSuffixedString(string $Ago='Ago', string $Until='Until'):
	string {

		$Calc = $this->Calc();
		$LabelSet = $this->GetLabelSet();
		$Key = NULL;

		// run throught the units.

		foreach(['Y', 'D', 'H', 'M', 'S'] as $Key)
		if($Calc[$Key] !== 0)
		$Output[] = "{$Calc[$Key]}{$this->LabelSep}{$LabelSet[$Key]}";

		// compile the final string.

		$String = join($this->UnitSep, $Output);
		$String .= (($Calc['Dir'] > 0) ? " {$Ago}" : " {$Until}");

		return $String;
	}

}
