<?php

namespace Nether\Common;

use PHPUnit\Framework\TestCase;

class CommonLibTest
extends Library {

	public function
	OnLoad(...$Argv):
	void {

		parent::OnLoad(...$Argv);
		static::Set('Loaded', TRUE);
		static::Set('Prepared', FALSE);
		static::Set('OKThen', NULL);

		return;
	}

	public function
	OnPrepare(...$Argv):
	void {

		parent::OnPrepare(...$Argv);
		static::Set('Prepared', TRUE);
		static::Set('Neat', 'Neat');

		return;
	}

	public function
	OnReady(...$Argv):
	void {

		parent::OnReady(...$Argv);
		static::Set('Ready', TRUE);

		return;
	}

}

class LibraryTest
extends TestCase {

	/** @test */
	public function
	TestBasic():
	void {

		$Lib = new CommonLibTest;
		$this->AssertInstanceOf(Datastore::class, $Lib::$Config);
		$this->AssertInstanceOf(Datastore::class, $Lib::Config());

		////////

		$this->AssertTrue($Lib::Get('Loaded'));
		$this->AssertFalse($Lib::Get('Prepared'));
		$this->AssertNull($Lib::Get('Ready'));
		$this->AssertNull($Lib::Get('Neat'));

		////////

		$this->AssertNull($Lib::Get('Test'));

		$Lib::Set('Test', 'OK');
		$this->AssertEquals('OK', $Lib::Get('Test'));

		////////

		$Lib->OnPrepare();
		$this->AssertTrue($Lib::Get('Loaded'));
		$this->AssertTrue($Lib::Get('Prepared'));
		$this->AssertNull($Lib::Get('Ready'));
		$this->AssertEquals('Neat', $Lib::Get('Neat'));


		$Lib->OnReady();
		$this->AssertTrue($Lib::Get('Loaded'));
		$this->AssertTrue($Lib::Get('Prepared'));
		$this->AssertTRUE($Lib::Get('Ready'));
		$this->AssertEquals('Neat', $Lib::Get('Neat'));

		return;
	}

	/** @test */
	public function
	TestExistingConf():
	void {

		$Lib = new CommonLibTest(Config: new Datastore([
			'DataFromOtherThings'=> TRUE
		]));

		$this->AssertTrue($Lib::Get('Loaded'));
		$this->AssertFalse($Lib::Get('Prepared'));
		$this->AssertNull($Lib::Get('Ready'));
		$this->AssertNull($Lib::Get('Neat'));
		$this->AssertTrue($Lib::Get('DataFromOtherThings'));

		$this->AssertTrue($Lib::Has('Loaded'));
		$this->AssertTrue($Lib::Has('Prepared'));

		// test a null value set in the load.

		$this->AssertFalse($Lib::Has('OKThen'));
		$this->AssertTrue($Lib::Has('OKThen', TRUE));

		// test a value never set because we never prepared the lib.

		$this->AssertFalse($Lib::Has('Neat'));
		$this->AssertFalse($Lib::Has('Neat'), TRUE);

		return;
	}

}