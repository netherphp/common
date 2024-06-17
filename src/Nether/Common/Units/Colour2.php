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

	#[Common\Meta\Date('2024-06-17')]
	protected float
	$V;

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

	#[Common\Meta\Date('2024-06-17')]
	public function
	V():
	float {

		return $this->V;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Common\Meta\Date('2024-06-17')]
	#[Common\Meta\Info('Degrees - Range: -359 <-> 359')]
	public function
	HueRotate(int $Deg=0):
	static {

		$Hue = ($this->H + $Deg) % 360;

		if($Hue < 0.0)
		$Hue += 360;

		////////

		$this->H = $Hue;
		$this->UpdateFromHSL();

		return $this;
	}

	#[Common\Meta\Date('2024-06-17')]
	#[Common\Meta\Info('Percentage - Range: -1.0 <-> 1.0')]
	public function
	HueShift(float $Percent=0.0):
	static {

		$this->HueRotate(
			round((360 * $Percent), 0)
		);

		return $this;
	}

	#[Common\Meta\Date('2024-06-17')]
	#[Common\Meta\Info('Percentage - Range: 0.0 <-> 1.0')]
	public function
	Saturate(float $Percent=0.0):
	static {

		$Sat = static::ClampNormal(
			$this->S + ($this->S * $Percent)
		);

		$this->S = $Sat;
		$this->UpdateFromHSL();

		return $this;
	}

	#[Common\Meta\Date('2024-06-17')]
	#[Common\Meta\Info('Percentage - Range: 0.0 <-> 1.0')]
	public function
	Desaturate(float $Percent=0.0):
	static {

		$Sat = static::ClampNormal(
			$this->S - ($this->S * $Percent)
		);

		$this->S = $Sat;
		$this->UpdateFromHSL();

		return $this;
	}

	#[Common\Meta\Date('2024-06-17')]
	#[Common\Meta\Info('Percentage - Range: 0.0 <-> 1.0')]
	public function
	Lighten(float $Percent=0.0):
	static {

		$Lum = static::ClampNormal(
			$this->L + ($this->L * $Percent)
		);

		$this->L = $Lum;

		return $this;
	}

	#[Common\Meta\Date('2024-06-17')]
	#[Common\Meta\Info('Percentage - Range: 0.0 <-> 1.0')]
	public function
	Darken(float $Percent=0.0):
	static {

		$Lum = static::ClampNormal(
			$this->L - ($this->L * $Percent)
		);

		$this->L = $Lum;
		$this->UpdateFromHSL();

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Common\Meta\Date('2024-06-17')]
	#[Common\Meta\Info('Multiplier - Range: 0.0 -> INF')]
	public function
	Saturation(float $Mult=1.0):
	static {

		$Sat = static::ClampNormal(
			$this->S * $Mult
		);

		$this->S = $Sat;
		$this->UpdateFromHSL();

		return $this;
	}

	#[Common\Meta\Date('2024-06-17')]
	#[Common\Meta\Info('Multiplier - Range: 0.0 -> INF')]
	public function
	Brightness(float $Mult=1.0):
	static {

		$Lum = static::ClampNormal(
			$this->L * $Mult
		);

		$this->L = $Lum;
		$this->UpdateFromHSL();

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Common\Meta\Date('2024-06-15')]
	protected function
	CalcHueFromRGB():
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

		return static::WrapDegrees($Hue);
	}

	#[Common\Meta\Date('2024-06-15')]
	protected function
	CalcSatFromRGB():
	float {

		$Max = max($this->Rn, $this->Gn, $this->Bn);
		$Min = min($this->Rn, $this->Gn, $this->Bn);
		$Lum = $this->CalcLumFromRGB();
		$Sat = 0.0;

		////////

		if($Min === $Max)
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
	CalcLumFromRGB():
	float {

		$Min = min($this->Rn, $this->Gn, $this->Bn);
		$Max = max($this->Rn, $this->Gn, $this->Bn);
		$Lum = ($Max + $Min) * 0.5;

		return $Lum;
	}

	#[Common\Meta\Date('2024-06-17')]
	protected function
	CalcValFromRGB():
	float {

		$Max = max($this->Rn, $this->Gn, $this->Bn);

		return $Max;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Common\Meta\Date('2024-06-15')]
	protected function
	UpdateFromRGB():
	static {

		$this->UpdateRGBNormals();

		$this->H = $this->CalcHueFromRGB();
		$this->S = $this->CalcSatFromRGB();
		$this->L = $this->CalcLumFromRGB();
		$this->V = $this->CalcValFromRGB();

		return $this;
	}

	#[Common\Meta\Date('2024-06-16')]
	protected function
	UpdateFromHSL():
	static {

		// none of this was obvious math. this was written by following
		// an explained algo.

		$Hue = $this->H();
		$Sat = $this->S();
		$Lum = $this->L();

		$Rot = ($Hue / 360.0);
		$RGB = [];
		$T1 = NULL;
		$T2 = NULL;

		////////

		if($Sat === 0.0) {
			$this->R = static::ClampByte($Lum * 255);
			$this->G = $this->R;
			$this->B = $this->R;
			$this->UpdateRGBNormals();
			return $this;
		}

		////////

		$T1 = match(TRUE) {
			($Lum < 0.5)
			=> $Lum * (1.0 + $Sat),

			default
			=> ($Lum + ($Sat - ($Lum * $Sat)))
		};

		$T2 = (2 * $Lum) - $T1;

		////////

		$RGB[0] = $Rot + 0.333;
		$RGB[1] = $Rot;
		$RGB[2] = $Rot - 0.333;

		$RGB = array_map(
			fn(float $V)=> match(TRUE) {
				($V < 0.0) => ($V + 1.0),
				($V > 1.0) => ($V - 1.0),
				default    => $V
			},
			$RGB
		);

		$RGB = array_map(
			fn(float $V)=> match(TRUE) {
				(($V * 6.0) < 1.0)
				=> $T2 + (($T1 - $T2) * 6.0 * $V),

				(($V * 2.0) < 1.0)
				=> $T1,

				(($V * 3.0) < 2.0)
				=> $T2 + (($T1 - $T2) * (0.666 - $V) * 6.0),

				default
				=> $T2
			},
			$RGB
		);

		$RGB = array_map(
			fn(float $V)=> static::ClampByte(
				round(($V * 255), 0)
			),
			$RGB
		);

		////////

		$this->R = $RGB[0];
		$this->G = $RGB[1];
		$this->B = $RGB[2];

		$this->UpdateRGBNormals();

		////////

		return $this;
	}

	#[Common\Meta\Date('2024-06-15')]
	protected function
	UpdateRGBNormals():
	static {

		$this->Rn = $this->R / 255.0;
		$this->Gn = $this->G / 255.0;
		$this->Bn = $this->B / 255.0;
		$this->An = $this->A / 255.0;

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

		$this->UpdateFromRGB();

		return $this;
	}

	#[Common\Meta\Date('2024-06-15')]
	public function
	ImportIntRGB(int $Int):
	static {

		list($this->R, $this->G, $this->B) = static::DecToBitsRGB($Int);
		$this->A = 0xFF;

		$this->UpdateFromRGB();

		return $this;
	}

	#[Common\Meta\Date('2024-06-15')]
	public function
	ImportIntRGBA(int $Int):
	static {

		list($this->R, $this->G, $this->B) = static::DecToBitsRGBA($Int);
		$this->A = 0xFF;

		$this->UpdateFromRGB();

		return $this;
	}

	#[Common\Meta\Date('2024-06-16')]
	public function
	ImportRGBA(int $R, int $G, int $B, int $A=255):
	static {

		$this->ImportIntRGBA(
			($R << 24) | ($G << 16) | ($B << 8) | ($A << 0)
		);

		return $this;
	}

	#[Common\Meta\Date('2024-06-16')]
	public function
	ImportHSL(int $H, float $S, float $L):
	static {

		$this->H = $H % 360;
		$this->S = min(1.0, max(0.0, $S));
		$this->L = min(1.0, max(0.0, $L));
		$this->A = 0xFF;

		$this->UpdateFromHSL();

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

	#[Common\Meta\Date('2024-06-16')]
	static public function
	FromHSL(int $H, float $S, float $L):
	static {

		$Output = new static;
		$Output->ImportHSL($H, $S, $L);

		return $Output;
	}

	#[Common\Meta\Date('2024-06-15')]
	static public function
	FromRGBA(int $R, int $G, int $B, int $A=255):
	static {

		$Output = new static;
		$Output->ImportRGBA($R, $G, $B, $A);

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

	#[Common\Meta\Date('2024-06-16')]
	static public function
	ClampByte(int|float $Num):
	int {

		$Num = (int)$Num;

		////////

		if($Num > 255)
		$Num = 255;

		if($Num < 0)
		$Num = 0;

		////////

		return $Num;
	}

	#[Common\Meta\Date('2024-06-17')]
	static public function
	ClampNormal(float $Val):
	float {

		return min(max($Val, 0.0), 1.0);
	}

	static public function
	WrapDegrees(int|float $Deg):
	int {

		if(is_float($Deg))
		$Deg = (int)round($Deg, 0);

		////////

		$Deg = $Deg % Common\Values::CircleDegrees;

		if($Deg < 0)
		$Deg += Common\Values::CircleDegrees;

		return $Deg;
	}

};
