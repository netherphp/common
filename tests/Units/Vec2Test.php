<?php

namespace NetherTestSuite\Common;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use PHPUnit;

use Nether\Common\Units\Vec2;
use Exception;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class Vec2Test
extends PHPUnit\Framework\TestCase {

	/** @test */
	public function
	TestBasic():
	void {

		$X = 42;
		$Y = 69;

		$Vec = new Vec2($X, $Y);
		$Exceptional = FALSE;
		$Doh = NULL;
		$Err = NULL;

		// read the properties directly.

		$this->AssertEquals($X, $Vec->X);
		$this->AssertEquals($Y, $Vec->Y);

		// read context sugar properties that do the same thing.

		$this->AssertEquals($X, $Vec->Min);
		$this->AssertEquals($Y, $Vec->Max);

		// read context sugar properties that don't exist.

		try { $Doh = $Vec->OopsDidItAgain; }
		catch(Exception $Err) { $Exceptional = TRUE; }

		$this->AssertTrue($Exceptional);
		$this->AssertInstanceOf(Exception::class, $Err);

		// test direct assignment.

		$Vec->X = ($X = 123.4);
		$Vec->Y = ($Y = 321.0);

		$this->AssertEquals($X, $Vec->X);
		$this->AssertEquals($Y, $Vec->Y);

		return;
	}

	/** @test */
	public function
	TestClamp():
	void {

		$X = 42;
		$Y = 69;

		$CX = 50;
		$CY = 60;

		// test doing things lame

		$Vec = new Vec2($X, $Y);
		$Vec->ClampX($CX, $CY);
		$Vec->ClampY($CX, $CY);

		$this->AssertEquals($CX, $Vec->X);
		$this->AssertEquals($CY, $Vec->Y);

		// test doing things epic

		$Vec = Vec2::Coord($X, $Y);
		$Vec->Clamp(
			Vec2::Range($CX, $CY),
			Vec2::Range($CX, $CY)
		);

		$this->AssertEquals($CX, $Vec->X);
		$this->AssertEquals($CY, $Vec->Y);

		// test doing things very iteratively.

		$Vec = new Vec2($X, $Y);
		$Vec->Clamp(X: Vec2::Range($CX, $CY));

		$this->AssertEquals(50, $Vec->X);
		$this->AssertEquals(69, $Vec->Y);

		$Vec->X = $CX + 5;
		$Vec->Clamp(Y: Vec2::Range($CX, $CY));

		$this->AssertEquals(($CX + 5), $Vec->X);
		$this->AssertEquals($CY, $Vec->Y);

		return;
	}

}