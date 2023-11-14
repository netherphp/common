<?php

namespace Nether\Common;

use PHPUnit\Framework\TestCase;

class UuidUtilTest
extends TestCase {

	/** @test */
	public function
	TestUUID():
	void {

		$UUID = UUID::V4();
		$this->AssertTrue(is_string($UUID));
		$this->AssertEquals(36, strlen($UUID));
		$this->AssertEquals(4, substr_count($UUID, '-'));
		$this->AssertEquals(1, preg_match('#^[a-f0-9\\-]{36}$#', $UUID));

		$First = $UUID;
		$UUID = UUID::V7();
		$this->AssertTrue($First !== $UUID);
		$this->AssertTrue(is_string($UUID));
		$this->AssertEquals(36, strlen($UUID));
		$this->AssertEquals(4, substr_count($UUID, '-'));
		$this->AssertEquals(1, preg_match('#^[a-f0-9\\-]{36}$#', $UUID));

		return;
	}

	/** @test */
	public function
	TestForElementHTML():
	void {

		$UUID = UUID::ForElementHTML();
		$this->AssertTrue(is_string($UUID));
		$this->AssertEquals(35, strlen($UUID));
		$this->AssertEquals(1, substr_count($UUID, '-'));
		$this->AssertEquals(1, preg_match('#^el-[a-f0-9]{32}$#', $UUID));

		return;
	}

}