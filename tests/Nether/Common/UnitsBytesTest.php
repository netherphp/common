<?php

namespace Nether\Common;

use PHPUnit\Framework\TestCase;

class UnitsBytesTest
extends TestCase {

	/** @test */
	public function
	TestBasic():
	void {

		$Bytes = new Units\Bytes(69);

		$this->AssertEquals('69 b', $Bytes->Get());
		$this->AssertEquals('69 b', (string)$Bytes);
		$this->AssertEquals('69 b', $Bytes());

		return;
	}

	/** @test */
	public function
	TestMagnitudes():
	void {

		$Bytes = new Units\Bytes(1024);

		$Bytes->SetStyleIEC();
		$this->AssertEquals('1 KiB', $Bytes->Get());

		$Bytes->SetStyleMetric();
		$this->AssertEquals('1.02 KB', $Bytes->Get());

		////////

		$Bytes = new Units\Bytes(1024*1024);

		$Bytes->SetStyleIEC();
		$this->AssertEquals('1 MiB', $Bytes->Get());

		$Bytes->SetStyleMetric();
		$this->AssertEquals('1.05 MB', $Bytes->Get());

		return;
	}

	/** @test */
	public function
	TestLabels():
	void {

		$Bytes = new Units\Bytes(1024);

		$this->AssertEquals('1 KiB', $Bytes->Get());

		$Bytes->SetLabelCase(-1);
		$this->AssertEquals('1 kib', $Bytes->Get());

		$Bytes->SetLabelCase(1);
		$this->AssertEquals('1 KIB', $Bytes->Get());


		$Bytes->SetLabelCase();
		$Bytes->SetLabelSep();
		$this->AssertEquals('1 KiB', $Bytes->Get());

		$Bytes->SetLabelSep('');
		$this->AssertEquals('1KiB', $Bytes->Get());

		return;
	}

}