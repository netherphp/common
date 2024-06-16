<?php

namespace NetherTestSuite\Common\Datastore;

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
	TestReset():
	void {

		$Data = new Common\Datastore([ 1, 2, 3, 4 ]);

		$Data->Next();
		$Data->Next();
		$this->AssertEquals(3, $Data->Current());

		$Data->Reset();
		$this->AssertEquals(1, $Data->Current());

		return;
	}

	/** @test */
	public function
	TestIsEmpty():
	void {

		$Data = new Common\Datastore;
		$this->AssertTrue($Data->IsEmpty());
		$this->AssertFalse($Data->IsNotEmpty());

		$Data->Push(1);
		$this->AssertFalse($Data->IsEmpty());
		$this->AssertTrue($Data->IsNotEmpty());

		return;
	}

	/** @test */
	public function
	TestCopy():
	void {

		$Data = new Common\Datastore([ 1, 2, 3, 4 ]);
		$More = $Data->Copy();

		$this->AssertTrue(spl_object_id($Data) !== spl_object_id($More));
		$this->AssertCount(4, $More);

		$More[0] = 9;

		$this->AssertEquals(1, $Data[0]);
		$this->AssertEquals(9, $More[0]);

		return;
	}

	/** @test */
	public function
	TestHasAnyKey():
	void {

		$Data = new Common\Datastore([
			'k1'=> 1, 'k2'=> 2, 'k3'=> 3
		]);

		$this->AssertTrue($Data->HasAnyKey('k1', 'k2'));
		$this->AssertTrue($Data->HasAnyKey([ 'k2', 'k1' ]));

		$this->AssertFalse($Data->HasAnyKey('k8', 'k9'));
		$this->AssertFalse($Data->HasAnyKey([ 'k8', 'k9' ]));

		return;
	}

	/** @test */
	public function
	TestFromString():
	void {

		$Letters = Common\Datastore::FromString('asdf');

		$this->AssertEquals('a', $Letters[0]);
		$this->AssertEquals('f', $Letters[3]);

		$Words = Common\Datastore::FromString('a s d f', ' ');

		$this->AssertEquals('a', $Words[0]);
		$this->AssertEquals('f', $Words[3]);

		return;
	}

};

