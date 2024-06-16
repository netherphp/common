<?php

namespace Nether\Common\Units;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Nether\Common;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

#[Common\Meta\Date('2024-06-15')]
class Colour2 {

	#[Common\Meta\Date('2024-06-15')]
	protected int
	$R;

	#[Common\Meta\Date('2024-06-15')]
	protected float
	$Rn;

	#[Common\Meta\Date('2024-06-15')]
	protected int
	$G;

	#[Common\Meta\Date('2024-06-15')]
	protected float
	$Gn;

	#[Common\Meta\Date('2024-06-15')]
	protected int
	$B;

	#[Common\Meta\Date('2024-06-15')]
	protected float
	$Bn;

	#[Common\Meta\Date('2024-06-15')]
	protected int
	$A;

	#[Common\Meta\Date('2024-06-15')]
	protected float
	$An;

	#[Common\Meta\Date('2024-06-15')]
	protected int
	$H;

	#[Common\Meta\Date('2024-06-15')]
	protected float
	$S;

	#[Common\Meta\Date('2024-06-15')]
	protected float
	$L;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Common\Meta\Date('2024-06-15')]
	public function
	R():
	int {

		return $this->R;
	}

	#[Common\Meta\Date('2024-06-15')]
	public function
	G():
	int {

		return $this->G;
	}

	#[Common\Meta\Date('2024-06-15')]
	public function
	B():
	int {

		return $this->B;
	}

	#[Common\Meta\Date('2024-06-15')]
	public function
	A():
	int {

		return $this->A;
	}

	#[Common\Meta\Date('2024-06-15')]
	public function
	H():
	int {

		return $this->H;
	}

	#[Common\Meta\Date('2024-06-15')]
	public function
	S():
	float {

		return $this->S;
	}

	#[Common\Meta\Date('2024-06-15')]
	public function
	L():
	float {

		return $this->L;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Rotate(float $Percent):
	static {

		throw new Common\Error\MethodNotFound('Rotate');

		return $this;
	}

	public function
	Saturate(float $Percent):
	static {

		throw new Common\Error\MethodNotFound('Saturate');

		return $this;
	}

	public function
	Darken(float $Percent):
	static {

		throw new Common\Error\MethodNotFound('Darken');

		return $this;
	}

	public function
	Lighten(float $Percent):
	static {

		throw new Common\Error\MethodNotFound('Lighten');

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Common\Meta\Date('2024-06-15')]
	protected function
	CalcHue():
	int {

		$Hue = 0;
		$Min = min($this->Rn, $this->Gn, $this->Bn);
		$Max = max($this->Rn, $this->Gn, $this->Bn);
		$Gap = $Max - $Min;

		////////

		if($Gap === 0.0)
		$Hue = 0;

		elseif($Max === $this->Rn)
		$Hue = 60 * (($this->Gn - $this->Bn) / $Gap);

		elseif($Max === $this->Gn)
		$Hue = 60 * (2.0 + (($this->Bn - $this->Rn) / $Gap));

		elseif($Max === $this->Bn)
		$Hue = 60 * (4.0 + (($this->Rn - $this->Gn) / $Gap));

		////////

		if($Hue < 0)
		$Hue += 360;

		////////

		return Common\Filters\Numbers::IntRange(
			$Hue, 0, 360
		);
	}

	#[Common\Meta\Date('2024-06-15')]
	protected function
	CalcSat():
	float {

		$Max = max($this->Rn, $this->Gn, $this->Bn);
		$Min = min($this->Rn, $this->Gn, $this->Bn);
		$Lum = $this->CalcLum();
		$Sat = 0.0;

		////////

		if($Max === 0.0)
		$Sat = 0.0;

		elseif($Lum <= 0.5)
		$Sat = ($Max - $Min) / ($Max + $Min);

		elseif($Lum > 0.5)
		$Sat = ($Max - $Min) / (2.0 - $Max - $Min);

		////////

		return $Sat;
	}

	#[Common\Meta\Date('2024-06-15')]
	protected function
	CalcLum():
	float {

		$Min = min($this->Rn, $this->Gn, $this->Bn);
		$Max = max($this->Rn, $this->Gn, $this->Bn);
		$Lum = ($Max + $Min) * 0.5;

		return $Lum;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Common\Meta\Date('2024-06-15')]
	protected function
	UpdateHSL():
	static {

		$this->H = $this->CalcHue();
		$this->S = $this->CalcSat();
		$this->L = $this->CalcLum();

		return $this;
	}

	#[Common\Meta\Date('2024-06-15')]
	protected function
	UpdateNormals():
	static {

		$this->Rn = $this->R / 255.0;
		$this->Gn = $this->G / 255.0;
		$this->Bn = $this->B / 255.0;
		$this->An = $this->A / 255.0;

		return $this;
	}

	#[Common\Meta\Date('2024-06-15')]
	protected function
	UpdateInts():
	static {

		$this->R = Common\Filters\Numbers::IntRange(
			($this->Rn * 255), 0, 255
		);

		$this->G = Common\Filters\Numbers::IntRange(
			($this->Gn * 255), 0, 255
		);

		$this->B = Common\Filters\Numbers::IntRange(
			($this->Bn * 255), 0, 255
		);

		$this->A = Common\Filters\Numbers::IntRange(
			($this->An * 255), 0, 255
		);

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Common\Meta\Date('2024-06-15')]
	public function
	ImportHexString(string $Hex):
	static {

		$Hex = ltrim($Hex, '#');
		$Len = strlen($Hex);

		if(preg_match('/[^0-9a-zA-Z]/', $Hex))
		throw new Common\Error\FormatInvalid('invalid hex string');

		////////

		list($this->R, $this->G, $this->B, $this->A)
		= match(TRUE) {
			($Len === 8) => static::DecToBitsRGBA(hexdec($Hex)),
			($Len === 6) => [ ...static::DecToBitsRGB(hexdec($Hex)), 0xFF ],
			($Len === 4) => static::ShortToBitsRGBA(hexdec($Hex)),
			($Len === 3) => [ ...static::ShortToBitsRGB(hexdec($Hex)), 0xFF ],

			default
			=> throw new Common\Error\FormatInvalid('invalid hex colour format')
		};

		////////

		$this->UpdateNormals();
		$this->UpdateHSL();

		return $this;
	}

	#[Common\Meta\Date('2024-06-15')]
	public function
	ImportIntRGB(int $Int):
	static {

		list($this->R, $this->G, $this->B) = static::DecToBitsRGB($Int);
		$this->A = 0xFF;

		$this->UpdateNormals();
		$this->UpdateHSL();

		return $this;
	}

	#[Common\Meta\Date('2024-06-15')]
	public function
	ImportIntRGBA(int $Int):
	static {

		list($this->R, $this->G, $this->B) = static::DecToBitsRGBA($Int);
		$this->A = 0xFF;

		$this->UpdateNormals();
		$this->UpdateHSL();

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Common\Meta\Date('2024-06-15')]
	public function
	ToHexRGB():
	string {

		$Output = strtoupper(sprintf(
			'#%02x%02x%02x',
			$this->R,
			$this->G,
			$this->B
		));

		return $Output;
	}

	#[Common\Meta\Date('2024-06-15')]
	public function
	ToHexRGBA():
	string {

		$Output = strtoupper(sprintf(
			'#%02x%02x%02x%02x',
			$this->R,
			$this->G,
			$this->B,
			$this->A
		));

		return $Output;
	}

	#[Common\Meta\Date('2024-06-15')]
	public function
	ToIntRGB():
	int {

		return (0
			| ($this->R << 16)
			| ($this->G << 8)
			| ($this->B)
		);
	}

	#[Common\Meta\Date('2024-06-15')]
	public function
	ToIntRGBA():
	int {

		return (0
			| ($this->R << 24)
			| ($this->G << 16)
			| ($this->B << 8)
			| ($this->A)
		);
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Common\Meta\Date('2024-06-15')]
	static public function
	FromHexString(string $RGBa):
	static {

		$Output = new static;
		$Output->ImportHexString($RGBa);

		return $Output;
	}

	#[Common\Meta\Date('2024-06-15')]
	static public function
	FromIntRGB(int $RGB):
	static {

		$Output = new static;
		$Output->ImportIntRGB($RGB);

		return $Output;
	}

	#[Common\Meta\Date('2024-06-15')]
	static public function
	FromIntRGBA(int $RGBA):
	static {

		$Output = new static;
		$Output->ImportIntRGBA($RGBA);

		return $Output;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Common\Meta\Date('2024-06-15')]
	static public function
	DecToBitsRGB(int $Int):
	array {

		return [
			($Int & 0xFF0000) >> 16,
			($Int & 0x00FF00) >> 8,
			($Int & 0x0000FF) >> 0
		];
	}

	#[Common\Meta\Date('2024-06-15')]
	static public function
	DecToBitsRGBA(int $Int):
	array {

		return [
			($Int & 0xFF000000) >> 24,
			($Int & 0x00FF0000) >> 16,
			($Int & 0x0000FF00) >> 8,
			($Int & 0x000000FF) >> 0
		];
	}

	#[Common\Meta\Date('2024-06-15')]
	static public function
	ShortToBitsRGB(int $Int):
	array {

		return [
			(($Int & 0xF00) >> 8) | (($Int & 0xF00) >> 4),
			(($Int & 0x0F0) >> 4) | (($Int & 0x0F0) >> 0),
			(($Int & 0x00F) >> 0) | (($Int & 0x00F) << 4)
		];
	}

	#[Common\Meta\Date('2024-06-15')]
	static public function
	ShortToBitsRGBA(int $Int):
	array {

		return [
			(($Int & 0xF000) >> 12) | (($Int & 0xF000) >> 8),
			(($Int & 0x0F00) >> 8)  | (($Int & 0x0F00) >> 4),
			(($Int & 0x00F0) >> 4)  | (($Int & 0x00F0) >> 0),
			(($Int & 0x000F) >> 0)  | (($Int & 0x000F) << 4)
		];
	}

};
