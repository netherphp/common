<?php

namespace Nether\Common\Units;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Nether\Common;

use Exception;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class Vec2
implements
	Common\Interfaces\PropertyInfoPackage,
	Common\Interfaces\ToArray,
	Common\Interfaces\ToJSON {

	#[Common\Meta\PropertyListable]
	public int|float
	$X;

	#[Common\Meta\PropertyListable]
	public int|float
	$Y;

	use
	Common\Package\PropertyInfoPackage,
	Common\Package\ToArray,
	Common\Package\ToJSON;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(int|float $X=0, int|float $Y=0) {

		$this->X = $X;
		$this->Y = $Y;

		return;
	}

	public function
	__Get(string $Key):
	mixed {

		return match($Key) {
			'Min'   => $this->X,
			'Max'   => $this->Y,
			default => throw new Exception(sprintf(
				'%s is not a thing on %s',
				$Key, $this::class
			))
		};
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Clamp(self $X=NULL, self $Y=NULL):
	static {

		if($X instanceof self)
		$this->ClampX($X->X, $X->Y);

		if($Y instanceof self)
		$this->ClampY($Y->X, $Y->Y);

		return $this;
	}

	public function
	ClampX(int|float $Min, int|float $Max):
	static {

		$this->X = Common\Math::Clamp($this->X, $Min, $Max);

		return $this;
	}

	public function
	ClampY(int|float $Min, int|float $Max):
	static {

		$this->Y = Common\Math::Clamp($this->Y, $Min, $Max);

		return $this;
	}

	////////////////////////////////////////////////////////////////
	// CONTEXT SWEETENER API ///////////////////////////////////////

	// these methods exist purely to give the code creating vectors context
	// what they are doing.

	static public function
	Range(int|float $Min=0, int|float $Max=0):
	static {

		return new static($Min, $Max);
	}

	static public function
	Coord(int|float $X=0, int|float $Y=0):
	static {

		return new static($X, $Y);
	}

};
