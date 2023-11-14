<?php

namespace NetherTestSuite\Common;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Nether\Common\Units\Colour;
use PHPUnit\Framework\TestCase;
use ArgumentCountError;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class ColourTest
extends TestCase {

	/** @test */
	public function
	TestBasic():
	void {

		$Data = [
			'red'  => [ 255, 0, 0, 1.0 ],
			'lime' => [ 0, 255, 0, 1.0 ],
			'blue' => [ 0, 0, 255, 1.0 ]
		];

		$Name = NULL;
		$Expect = NULL;
		$Exhext = NULL;

		$RGB = NULL;
		$Hex = NULL;

		foreach($Data as $Name => $Expect) {
			$Exhext = sprintf('#%06s', dechex(0
				| ($Expect[0] << 16)
				| ($Expect[1] << 8)
				| ($Expect[2] << 0)
			));

			$Colour = new Colour($Name);
			$RGB = $Colour->GetRGB();
			$Hex = $Colour->GetHexRGB();

			$this->AssertEquals($Expect[0], $Colour->R());
			$this->AssertEquals($Expect[0], $RGB['R']);

			$this->AssertEquals($Expect[1], $Colour->G());
			$this->AssertEquals($Expect[1], $RGB['G']);

			$this->AssertEquals($Expect[2], $Colour->B());
			$this->AssertEquals($Expect[2], $RGB['B']);

			$this->AssertEquals($Expect[3], $Colour->A());
			$this->AssertEquals($Expect[3], $RGB['A']);

			$this->AssertEquals($Exhext, $Colour->GetHexRGB());
		}

		return;
	}

	/** @test */
	public function
	TestLightenDarkenDesat():
	void {

		$Red = new Colour('red');
		$this->AssertEquals(255, $Red->R());

		// i am not entirely certain about the lighten and darken math
		// but right now 25% lowers the red by half.

		$Red->Darken(25.0);
		$this->AssertEquals(128, $Red->R());

		$Red->Lighten(25.0);
		$this->AssertEquals(255, $Red->R());

		// the desaturate value almost makes sense, as of right now a
		// value of 100% lowers the red by half.

		$Red->Desaturate(100.0);
		$this->AssertEquals(128, $Red->R());

		return;
	}

	/** @test */
	public function
	TestRotate():
	void {

		$Red = new Colour('red');
		$this->AssertEquals(255, $Red->R());
		$this->AssertEquals(0, $Red->G());
		$this->AssertEquals(0, $Red->B());

		$Red->Rotate(120.0);
		$this->AssertEquals(0, $Red->R());
		$this->AssertEquals(255, $Red->G());
		$this->AssertEquals(0, $Red->B());

		$Red->Rotate(120.0);
		$this->AssertEquals(0, $Red->R());
		$this->AssertEquals(0, $Red->G());
		$this->AssertEquals(255, $Red->B());

		return;
	}

	/** @test */
	public function
	TestFromArray():
	void {

		$Red = Colour::FromArray([ 255, 0, 0 ]);
		$this->AssertEquals(255, $Red->R());
		$this->AssertEquals(0, $Red->G());
		$this->AssertEquals(0, $Red->B());
		$this->AssertEquals(1.0, $Red->A());

		$Ghost = Colour::FromArray([ 128, 128, 128, 0.1 ]);
		$this->AssertEquals(128, $Ghost->R());
		$this->AssertEquals(128, $Ghost->G());
		$this->AssertEquals(128, $Ghost->B());
		$this->AssertEquals(0.1, $Ghost->A());

		////////

		$Exceptional = FALSE;

		try { Colour::FromArray([ 1 ]); }
		catch(ArgumentCountError $Err) {
			$this->AssertInstanceOf(ArgumentCountError::class, $Err);
			$Exceptional = TRUE;
		}

		$this->AssertTrue($Exceptional);

		return;
	}

	/** @test */
	public function
	TestFromString():
	void {

		$Red = Colour::FromString('red');
		$this->AssertEquals(255, $Red->R());
		$this->AssertEquals(0, $Red->G());
		$this->AssertEquals(0, $Red->B());
		$this->AssertEquals(1.0, $Red->A());

		$Grn = Colour::FromString('#00ff00');
		$this->AssertEquals(0, $Grn->R());
		$this->AssertEquals(255, $Grn->G());
		$this->AssertEquals(0, $Grn->B());
		$this->AssertEquals(1.0, $Grn->A());

		$Blu = Colour::FromString('rgb(0,0,255)');
		$this->AssertEquals(0, $Blu->R());
		$this->AssertEquals(0, $Blu->G());
		$this->AssertEquals(255, $Blu->B());
		$this->AssertEquals(1.0, $Blu->A());

		$Ghost = Colour::FromString('rgba(128, 128, 128, 0.5)');
		$this->AssertEquals(128, $Ghost->R());
		$this->AssertEquals(128, $Ghost->G());
		$this->AssertEquals(128, $Ghost->B());
		$this->AssertEquals(0.5, $Ghost->A());

		return;
	}

}