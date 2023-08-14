<?php

namespace NetherTestSuite\Common;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Nether\Common;

use PHPUnit\Framework\TestCase;
use Exception;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class CrontabFileTest
extends TestCase {

	/** @test */
	public function
	TestBasic():
	void {

		$Data = [
			'',
			' ',
			'#comment',
			'# comment',
			'* * * * * six'
		];

		////////

		$File = Common\Struct\CrontabFile::FromArray($Data);
		$this->AssertEquals(5, $File->Count());

		$File->Clean();
		$this->AssertEquals(1, $File->Count());

		return;
	}

	/** @test */
	public function
	TestWrite():
	void {

		$Data = [ '* * * * * cmd' ];

		////////

		$File = Common\Struct\CrontabFile::FromArray($Data);

		$this->AssertTrue(TRUE);

		return;
	}

	/** @test */
	public function
	TestViaSystemUser():
	void {

		$File = Common\Struct\CrontabFile::FetchViaSystemUser();

		$this->AssertTrue($File instanceof Common\Struct\CrontabFile);

		if(PHP_OS_FAMILY === 'Windows') {
			$this->AssertEquals(0, $File->Count());
		}

		else {

		}

		return;
	}

	/** @test */
	public function
	TestFilterCrontabLine():
	void {

		$Data = [
			'',
			' ',
			'#comment',
			'# comment',
			'* * * * * six'
		];

		$Data = array_filter(
			$Data,
			Common\Struct\CrontabFile::FilterCrontabLine(...)
		);

		$this->AssertEquals(1, count($Data));

		return;
	}

}
