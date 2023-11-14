<?php

namespace NetherTestSuite\Common;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Nether\Common\Units\Bytes;
use PHPUnit\Framework\TestCase;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class BytesTest
extends TestCase {

	/** @test */
	public function
	TestBasic():
	void {

		$Bytes = new Bytes(69);

		$this->AssertEquals('69 b', $Bytes->Get());
		$this->AssertEquals('69 b', (string)$Bytes);
		$this->AssertEquals('69 b', $Bytes());

		return;
	}

	/** @test */
	public function
	TestMagnitudes():
	void {

		$Bytes = new Bytes(1024);

		$Bytes->SetStyleIEC();
		$this->AssertEquals('1 KiB', $Bytes->Get());

		$Bytes->SetStyleMetric();
		$this->AssertEquals('1.02 KB', $Bytes->Get());

		////////

		$Bytes = new Bytes(1024*1024);

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

		$Bytes = new Bytes(1024);

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

	/** @test */
	public function
	TestRelativeChecks():
	void {

		$Bytes = new Bytes(1024);
		$Bro = new Bytes(1024);
		$Big = new Bytes(2048);
		$Lil = new Bytes(512);

		$this->AssertTrue($Bytes->IsHeavierThan($Lil));
		$this->AssertTrue($Bytes->IsHeavierThan($Lil->GetBytes()));
		$this->AssertFalse($Bytes->IsLighterThan($Lil));
		$this->AssertFalse($Bytes->IsLighterThan($Lil->GetBytes()));

		$this->AssertFalse($Bytes->IsHeavierThan($Big));
		$this->AssertFalse($Bytes->IsHeavierThan($Big->GetBytes()));
		$this->AssertTrue($Bytes->IsLighterThan($Big));
		$this->AssertTrue($Bytes->IsLighterThan($Big->GetBytes()));

		$this->AssertFalse($Bytes->IsLighterThan($Bro));
		$this->AssertFalse($Bytes->IsHeavierThan($Bro));
		$this->AssertTrue($Bytes->IsTheSameAs($Bro));
		$this->AssertTrue($Bytes->IsTheSameAs($Bro->GetBytes()));

		return;
	}

	/** @test */
	public function
	TestFromInt():
	void {

		$Bytes = Bytes::FromInt(42);
		$this->AssertTrue($Bytes->GetBytes() === 42);

		return;
	}

}