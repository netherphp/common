<?php

namespace NetherTestSuite\Common\Units;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Nether\Common;

use Nether\Common\Units\Colour2;
use PHPUnit\Framework\TestCase;
use ArgumentCountError;
use Throwable;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class Colour2Test
extends TestCase {

	const
	GryHex1 = '#404040',
	RedHex1 = '#FF0000',
	GrnHex1 = '#00FF00',
	BluHex1 = '#0000FF',
	YelHex1 = '#FFFF00',
	OrnHex1 = '#FF8800',
	NvyHex1 = '#123456',
	GldHex1 = '#F1C232';

	const
	GryHex1F = '#404040FF',
	RedHex1F = '#FF0000FF',
	GrnHex1F = '#00FF00FF',
	BluHex1F = '#0000FFFF',
	YelHex1F = '#FFFF00FF',
	OrnHex1F = '#FF8800FF',
	NvyHex1F = '#123456FF',
	GldHex1F = '#F1C232FF';

	const
	GryInt1 = 0x404040,
	RedInt1 = 0xFF0000,
	GrnInt1 = 0x00FF00,
	BluInt1 = 0x0000FF,
	YelInt1 = 0xFFFF00,
	OrnInt1 = 0xFF8800,
	NvyInt1 = 0x123456,
	GldInt1 = 0xF1C232;

	const
	GryInt1F = 0x404040FF,
	RedInt1F = 0xFF0000FF,
	GrnInt1F = 0x00FF00FF,
	BluInt1F = 0x0000FFFF,
	YelInt1F = 0xFFFF00FF,
	OrnInt1F = 0xFF8800FF,
	NvyInt1F = 0x123456FF,
	GldInt1F = 0xF1C232FF;

	const
	RedHSL1 = [ 0,   1.00, 0.50 ],
	GrnHSL1 = [ 120, 1.00, 0.50 ],
	BluHSL1 = [ 240, 1.00, 0.50 ],
	YelHSL1 = [ 60,  1.00, 0.50 ],
	OrnHSL1 = [ 32,  1.00, 0.50 ],
	NvyHSL1 = [ 210, 0.65, 0.20 ],
	GryHSL1 = [ 0,   0.00, 0.25 ],
	GldHSL1 = [ 45,  0.87, 0.57 ];

	const
	SweepRGB1 = [
		self::RedHex1, self::GrnHex1, self::BluHex1,
		self::YelHex1, self::OrnHex1, self::NvyHex1,
		self::GryHex1, self::GldHex1
	];

	const
	SweepRGB1F = [
		self::RedHex1F, self::GrnHex1F, self::BluHex1F,
		self::YelHex1F, self::OrnHex1F, self::NvyHex1F,
		self::GryHex1F, self::GldHex1F
	];

	const
	SweepHSL1 = [
		self::RedHSL1, self::GrnHSL1, self::BluHSL1,
		self::YelHSL1, self::OrnHSL1, self::NvyHSL1,
		self::GryHSL1, self::GldHSL1
	];

	const
	SweepInt1 = [
		self::RedInt1, self::GrnInt1, self::BluInt1,
		self::YelInt1, self::OrnInt1, self::NvyInt1,
		self::GryInt1, self::GldInt1
	];

	const
	SweepInt1F = [
		self::RedInt1F, self::GrnInt1F, self::BluInt1F,
		self::YelInt1F, self::OrnInt1F, self::NvyInt1F,
		self::GryInt1F, self::GldInt1F
	];

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	/** @test */
	public function
	TestFromHexString():
	void {

		$Key = NULL;
		$RGB = NULL;
		$Err = NULL;
		$C = NULL;

		////////

		foreach(static::SweepRGB1 as $Key => $RGB) {
			$C = Colour2::FromHexString($RGB);
			$this->AssertEquals(static::SweepRGB1[$Key], $C->ToHexRGB());
			$this->AssertEquals(static::SweepRGB1F[$Key], $C->ToHexRGBA());
		}

		foreach(static::SweepRGB1F as $Key => $RGB) {
			$C = Colour2::FromHexString($RGB);
			$this->AssertEquals(static::SweepRGB1[$Key], $C->ToHexRGB());
			$this->AssertEquals(static::SweepRGB1F[$Key], $C->ToHexRGBA());
		}

		////////

		$Err = NULL;

		try { $Colour = Colour2::FromHexString('Ad'); }
		catch(Throwable $Err) { }

		$this->AssertInstanceOf(Common\Error\FormatInvalid::class, $Err);

		////////

		$Err = NULL;

		try { $Colour = Colour2::FromHexString('OK'); }
		catch(Throwable $Err) { }

		$this->AssertInstanceOf(Common\Error\FormatInvalid::class, $Err);

		return;
	}

	/** @test */
	public function
	TestFromIntRGB():
	void {

		$Key = NULL;
		$RGB = NULL;
		$C = NULL;

		////////

		foreach(static::SweepInt1 as $Key => $RGB) {
			$C = Colour2::FromIntRGB($RGB);
			$this->AssertEquals(static::SweepInt1[$Key], $C->ToIntRGB());
			$this->AssertEquals(static::SweepInt1F[$Key], $C->ToIntRGBA());
		}

		foreach(static::SweepInt1F as $Key => $RGB) {
			$C = Colour2::FromIntRGBA($RGB);
			$this->AssertEquals(static::SweepInt1[$Key], $C->ToIntRGB());
			$this->AssertEquals(static::SweepInt1F[$Key], $C->ToIntRGBA());
		}

		return;
	}

	/** @test */
	public function
	TestFromRGB():
	void {

		$Key = NULL;
		$RGB = NULL;
		$Bits = NULL;
		$C = NULL;

		////////

		foreach(static::SweepInt1 as $Key => $RGB) {
			$Bits = Colour2::DecToBitsRGB($RGB);
			$C = Colour2::FromRGBA($Bits[0], $Bits[1], $Bits[2]);
			$this->AssertEquals(static::SweepInt1[$Key], $C->ToIntRGB());
			$this->AssertEquals(static::SweepInt1F[$Key], $C->ToIntRGBA());
		}

		foreach(static::SweepInt1F as $Key => $RGB) {
			$Bits = Colour2::DecToBitsRGBA($RGB);
			$C = Colour2::FromRGBA($Bits[0], $Bits[1], $Bits[2], $Bits[3]);
			$this->AssertEquals(static::SweepInt1[$Key], $C->ToIntRGB());
			$this->AssertEquals(static::SweepInt1F[$Key], $C->ToIntRGBA());
		}

		return;
	}

	/** @test */
	public function
	TestFromHSL():
	void {

		$Fuzz = 2.25;
		$Key = NULL;
		$HSL = NULL;

		$Chsl = NULL;
		$Crgb = NULL;

		foreach(static::SweepHSL1 as $Key => $HSL) {
			$Chsl = Colour2::FromHSL($HSL[0], $HSL[1], $HSL[2]);
			$this->AssertEquals($HSL[0], $Chsl->H());
			$this->AssertEquals($HSL[1], $Chsl->S());
			$this->AssertEquals($HSL[2], $Chsl->L());
			$this->AssertEquals(255, $Chsl->A());

			$Crgb = Colour2::FromHexString(static::SweepRGB1[$Key]);
			$this->AssertEqualsWithDelta($Crgb->R(), $Chsl->R(), $Fuzz);
			$this->AssertEqualsWithDelta($Crgb->G(), $Chsl->G(), $Fuzz);
			$this->AssertEqualsWithDelta($Crgb->B(), $Chsl->B(), $Fuzz);
			$this->AssertEqualsWithDelta($Crgb->A(), $Chsl->A(), $Fuzz);
		}

		return;
	}

}