<?php

namespace Nether\Common\Units;

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
			'R' => $this->API->Red(),
			'G' => $this->API->Green(),
			'B' => $this->API->Blue()
		];
	}

	public function
	GetHexRGB():
	string {

		return sprintf(
			'#%s%s%s',
			dechex($this->API->Red()),
			dechex($this->API->Green()),
			dechex($this->API->Blue())
		);
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
