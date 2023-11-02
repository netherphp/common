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

		$Vec = new Vec2(42, 69);
		$Exceptional = FALSE;
		$Doh = NULL;
		$Err = NULL;

		// read the properties directly.

		$this->AssertEquals(42, $Vec->X);
		$this->AssertEquals(69, $Vec->Y);

		// read context sugar properties that do the same thing.

		$this->AssertEquals(42, $Vec->Min);
		$this->AssertEquals(69, $Vec->Max);

		// read context sugar properties that don't exist.

		try { $Doh = $Vec->OopsDidItAgain; }
		catch(Exception $Err) { $Exceptional = TRUE; }

		$this->AssertTrue($Exceptional);
		$this->AssertInstanceOf(Exception::class, $Err);

		return;
	}

	/** @test */
	public function
	TestClamp():
	void {

		// test doing things lame

		$Vec = new Vec2(42, 69);
		$Vec->ClampX(50, 60);
		$Vec->ClampY(50, 60);

		$this->AssertEquals(50, $Vec->X);
		$this->AssertEquals(60, $Vec->Y);

		// test doing things epic

		$Vec = Vec2::Coord(42, 69);
		$Vec->Clamp(Vec2::Range(50, 60), Vec2::Range(50, 60));

		$this->AssertEquals(50, $Vec->X);
		$this->AssertEquals(60, $Vec->Y);

		// test doing things very iteratively.

		$Vec = new Vec2(42, 69);
		$Vec->Clamp(X: Vec2::Range(50, 60));

		$this->AssertEquals(50, $Vec->X);
		$this->AssertEquals(69, $Vec->Y);

		$Vec->X = 55;
		$Vec->Clamp(Y: Vec2::Range(50, 60));

		$this->AssertEquals(55, $Vec->X);
		$this->AssertEquals(60, $Vec->Y);

		return;
	}

}