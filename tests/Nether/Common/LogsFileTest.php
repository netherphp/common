<?php

namespace Nether\Common;

use PHPUnit\Framework\TestCase;

class LogsFileTest
extends TestCase {

	/** @test */
	public function
	TestBasic():
	void {

		$Filename = Filesystem\Util::MkTempFile();
		$Log = new Logs\File('Test', $Filename);
		$Log->Write('this is a test');
		$Log->Flush();

		////////

		$Data = file_get_contents($Filename);
		$this->AssertTrue(str_contains($Data, 'this is a test'));

		////////

		unlink($Filename);
		return;
	}

	/** @test */
	public function
	TestTokens():
	void {

		$Date = explode('-', date('Y-m-d-H-i-s'));

		$Filename = Filesystem\Util::Pathify(
			sys_get_temp_dir(),
			'{Y}-{M}-{D}.log'
		);

		$Log = new Logs\File('Test', $Filename);
		$Log->Write('this is a test');
		$Log->Flush();

		////////

		$this->AssertTrue(
			str_contains(
				$Log->GetFilename(),
				"{$Date[0]}-{$Date[1]}-{$Date[2]}"
			),
			'imagine running this test the microsecond before midnight'
		);

		$Data = file_get_contents($Log->GetFilename());
		$this->AssertTrue(str_contains($Data, 'this is a test'));

		////////

		unlink($Log->GetFilename());
		return;
	}

}