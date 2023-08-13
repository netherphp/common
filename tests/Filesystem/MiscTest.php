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
	TestDirectoryClass():
	void {

		$Dir = new Common\Filesystem\Directory([
			'Path' => '/path',
			'Mode' => 0o755
		]);

		$Dir2 = new Common\Filesystem\Directory([
			'Path' => '/path',
			'Mode' => '0o755'
		]);


		$this->AssertEquals('/path', $Dir->Path);
		$this->AssertEquals(0o755, $Dir->Mode);
		$this->AssertEquals(0o755, $Dir2->Mode);

		$Array = json_decode(json_encode($Dir), TRUE);
		$this->AssertTrue(isset($Array['Path']));
		$this->AssertTrue(isset($Array['Mode']));

		return;
	}

	/** @test */
	public function
	TestSymlinkClass():
	void {

		$Link = new Common\Filesystem\Symlink([
			'Path'   => '/path',
			'Source' => '/source',
			'Mode'   => 0o755
		]);

		$Link2 = new Common\Filesystem\Symlink([
			'Path'   => '/path',
			'Source' => '/source',
			'Mode'   => '0o755'
		]);

		$this->AssertEquals('/path', $Link->Path);
		$this->AssertEquals('/source', $Link->Source);
		$this->AssertEquals(0o755, $Link->Mode);
		$this->AssertEquals(0o755, $Link2->Mode);

		$Array = json_decode(json_encode($Link), TRUE);
		$this->AssertTrue(isset($Array['Path']));
		$this->AssertTrue(isset($Array['Source']));
		$this->AssertTrue(isset($Array['Mode']));

		return;
	}

}