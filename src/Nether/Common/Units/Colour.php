<?php

namespace Nether\Common\Units;

use Nether\Common;
use OzdemirBurak\Iris;

class Colour {

	protected Iris\Color\Rgba
	$API;

	public function
	__Construct(mixed $Colour) {

		if(is_array($Colour))
		$Colour = match(count($Colour)) {
			4
			=> sprintf('rgba(%s)', join(',', $Colour)),

			default
			=> sprintf('rgb(%s)', join(',', $Colour))
		};

		$API = Iris\Color\Factory::Init($Colour);
		$this->API = $API->ToRGBA();

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	R():
	int {

		$Output = (int)$this->API->Red();

		return $Output;
	}

	public function
	G():
	int {

		$Output = (int)$this->API->Green();

		return $Output;
	}

	public function
	B():
	int {

		$Output = (int)$this->API->Blue();

		return $Output;
	}

	public function
	GetRGB():
	array {

		return [
			'R' => $this->R(),
			'G' => $this->G(),
			'B' => $this->B()
		];
	}

	#[Common\Meta\Date('2023-08-13')]
	public function
	GetHexRGB():
	string {

		return sprintf('#%06s', dechex(0
			| ($this->R() << 16)
			| ($this->G() << 8)
			| ($this->B() << 0)
		));
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Darken(float $Percent):
	static {

		$this->API = $this->API->Darken($Percent);

		return $this;
	}

	public function
	Lighten(float $Percent):
	static {

		$this->API = $this->API->Lighten($Percent);

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Desaturate(float $Percent):
	static {

		$this->API = $this->API->Desaturate($Percent);

		return $this;
	}

	public function
	Rotate(float $Deg):
	static {

		$this->API = $this->API->Spin($Deg);

		return $this;
	}

}
