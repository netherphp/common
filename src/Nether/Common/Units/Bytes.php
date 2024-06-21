<?php

namespace Nether\Common\Units;

use Nether\Common;

use Stringable;

class Bytes
implements Stringable {
/*//
@date 2022-11-11
given a number intending to be bytes this class will make it a readable
format. it can be used both one off or as like a printing/filter provider.
//*/

	const
	LabelCaseNormal = 0,
	LabelCaseUpper  = 1,
	LabelCaseLower  = -1;

	////////

	protected int
	$Bytes;

	protected int
	$Mult = 1024;

	protected string
	$LabelSep = ' ';

	protected int
	$LabelCase = 0;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(int $Bytes=0) {

		$this->Set($Bytes);
		$this->SetStyleIEC();

		return;
	}

	public function
	__ToString():
	string {

		return $this->Get();
	}

	public function
	__Invoke():
	?string {

		return $this->Get();
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Set(int $Bytes):
	static {

		$this->Bytes = $Bytes;
		return $this;
	}

	public function
	SetStyleMetric():
	static {

		$this->Mult = 1000;

		return $this;
	}

	public function
	SetStyleIEC():
	static {

		$this->Mult = 1024;

		return $this;
	}

	public function
	SetLabelCase(?int $How=NULL):
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

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Common\Meta\Date('2023-07-26')]
	public function
	IsHeavierThan(int|self $What):
	bool {

		if($What instanceof self)
		$What = $What->GetBytes();

		return ($this->Bytes > $What);
	}

	#[Common\Meta\Date('2023-07-26')]
	public function
	IsLighterThan(int|self $What):
	bool {

		if($What instanceof self)
		$What = $What->GetBytes();

		return ($this->Bytes < $What);
	}

	#[Common\Meta\Date('2023-07-26')]
	public function
	IsTheSameAs(int|self $What):
	bool {

		if($What instanceof self)
		$What = $What->GetBytes();

		return ($this->Bytes === $What);
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Get():
	string {

		$Mag = 8;
		$Label = NULL;
		$Val = (float)$this->Bytes;
		$Floor = NULL;

		while($Mag >= 1) {
			$Floor = pow($this->Mult, $Mag);

			if($this->Bytes >= $Floor) {
				$Label = $this->GetLabelByMag($Mag);
				$Val /= $Floor;
				break;
			}

			$Mag -= 1;
		}

		return sprintf(
			'%s%s%s',
			round($Val, 2),
			$this->LabelSep,
			($Label ?? $this->GetLabelByMag(0))
		);
	}

	#[Common\Meta\Date('2023-07-26')]
	public function
	GetBytes():
	int {

		return $this->Bytes;
	}

	public function
	GetLabelByMag(int $Mag):
	string {

		$Output = match($this->Mult) {
			1024 => match($Mag) {
				1 => 'KiB', 2 => 'MiB', 3 => 'GiB', 4 => 'TiB',
				5 => 'PiB', 6 => 'EiB', 7 => 'ZiB', 8 => 'YiB',
				default => 'b'
			},
			1000 => match($Mag) {
				1 => 'KB', 2 => 'MB', 3 => 'GB', 4 => 'TB',
				5 => 'PB', 6 => 'EB', 7 => 'ZB', 8 => 'YB',
				default => 'b'
			},
			default => 'b'
		};

		if($this->LabelCase === 1)
		$Output = strtoupper($Output);
		elseif($this->LabelCase === -1)
		$Output = strtolower($Output);

		return $Output;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Common\Meta\Date('2023-07-26')]
	static public function
	FromInt(int $Bytes):
	static {

		return new static($Bytes);
	}

	static public function
	FromReadable(string $Input):
	static {

		$Value = strtolower($Input);

		$Bytes = match(TRUE) {
			(str_ends_with($Value, 'p'))
			=> (int)$Input * pow(1024, 5),

			(str_ends_with($Value, 't'))
			=> (int)$Input * pow(1024, 4),

			(str_ends_with($Value, 'g'))
			=> (int)$Input * pow(1024, 3),

			(str_ends_with($Value, 'm'))
			=> (int)$Input * pow(1024, 2),

			(str_ends_with($Value, 'k'))
			=> (int)$Input * pow(1024, 1),

			default
			=> (int)$Input
		};

		return new static($Bytes);
	}

}
