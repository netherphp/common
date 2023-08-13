<?php

namespace NetherTestSuite\Common\Filesystem;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Nether\Common;

use PHPUnit\Framework\TestCase;
use Exception;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class MiscTest
extends TestCase {

	/** @test */
	public function
	TestSymlinkClass():
	void {

		$Link = new Common\Filesystem\Symlink([
			'Path'   => '/path',
			'Source' => '/source',
			'Mode'   => 0o755
		]);

		$this->AssertEquals('/path', $Link->Path);
		$this->AssertEquals('/source', $Link->Source);
		$this->AssertEquals(0o755, $Link->Mode);

		$Array = json_decode(json_encode($Link), TRUE);
		$this->AssertTrue(isset($Array['Path']));
		$this->AssertTrue(isset($Array['Source']));
		$this->AssertTrue(isset($Array['Mode']));

		return;
	}

}