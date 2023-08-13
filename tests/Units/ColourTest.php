<?php

namespace NetherTestSuite\Common;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Nether\Common\Units\Colour;
use PHPUnit\Framework\TestCase;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class ColourTest
extends TestCase {

	/** @test */
	public function
	TestBasic():
	void {

		$Data = [
			'red'  => [ 255, 0, 0 ],
			'lime' => [ 0, 255, 0 ],
			'blue' => [ 0, 0, 255 ]
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

			$this->AssertEquals($Exhext, $Colour->GetHexRGB());
		}

		return;
	}


}