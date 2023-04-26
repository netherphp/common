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

		$Data = file_get_contents($Filename);
		$this->AssertTrue(str_contains($Data, 'this is a test'));

		$this->AssertTrue(TRUE);
		unlink($Filename);

		return;
	}


}