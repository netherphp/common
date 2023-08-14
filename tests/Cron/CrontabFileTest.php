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
		$Expect = sprintf('%s%s', trim(join(PHP_EOL, $Data)), PHP_EOL);

		////////

		$File = Common\Struct\CrontabFile::FromArray($Data);
		$Temp = Common\Filesystem\Util::MkTempFile();

		$File->Write();
		$this->AssertTrue($File->GetFilename() !== NULL);
		$this->AssertEquals($Expect, file_get_contents($File->GetFilename()));
		unlink($File->GetFilename());

		$File->Write($Temp);
		$this->AssertTrue($File->GetFilename() === $Temp);
		$this->AssertEquals($Expect, file_get_contents($Temp));

		$File->Apply();

		unlink($Temp);

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
