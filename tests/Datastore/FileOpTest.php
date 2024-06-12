<?php

namespace NetherTestSuite\Common\Datastore;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Nether\Common;

use PHPUnit\Framework\TestCase;
use Exception;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class FileOpTest
extends TestCase {

	/** @test */
	public function
	TestReadPHSON():
	void {

		$File = @tempnam('/tmp', 'tmp');
		file_put_contents($File, serialize([ 1, 2, 3, 4 ]));
		rename($File, "{$File}.phson");

		$Data = Common\Datastore::FromFile("{$File}.phson");
		unlink("{$File}.phson");

		$this->AssertCount(4, $Data);
		$this->AssertEquals(1, $Data[0]);

		////////

		$File = @tempnam('/tmp', 'tmp');
		file_put_contents($File, serialize((object)[ 'k1'=> 1, 'k2'=> 2, 'k3'=> 3, 'k4'=> 4 ]));
		rename($File, "{$File}.phson");

		$Data = Common\Datastore::FromFile("{$File}.phson");
		unlink("{$File}.phson");

		$this->AssertCount(4, $Data);
		$this->AssertEquals(1, $Data['k1']);

		return;
	}

};

