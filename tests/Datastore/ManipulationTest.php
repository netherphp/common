<?php

namespace NetherTestSuite\Common\Datastore;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Nether\Common;

use PHPUnit\Framework\TestCase;
use Exception;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class ManipulationTest
extends TestCase {

	/** @test */
	public function
	TestDefine():
	void {

		$Store = new Common\Datastore;
		$this->AssertFalse(isset($Store['One']));
		$this->AssertFalse(isset($Store['Two']));
		$this->AssertFalse(isset($Store['Three']));

		// define it.

		$Store->Define('One', 1);
		$this->AssertEquals(1, $Store['One']);

		// fail to overwrite it.

		$Store->Define('One', 2);
		$this->AssertEquals(1, $Store['One']);

		// define a handful.

		$Store->Define([
			'One'   => 69,
			'Two'   => 2,
			'Three' => 3
		]);

		// fail to overwrite One

		$this->AssertEquals(1, $Store['One']);

		// defined the others.

		$this->AssertEquals(2, $Store['Two']);
		$this->AssertEquals(3, $Store['Three']);

		////////

		return;
	}

	/** @test */
	public function
	TestHeadMethodsAgainstLists():
	void {

		$Data = [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ];
		$Val = NULL;

		$Store = Common\Datastore::FromArray($Data);
		$this->AssertEquals(10, $Store->Count());

		// [ 1, 2, 3, 4, 5 ]

		$Store->HeadCrop(5);
		$this->AssertEquals(5, $Store->Count());
		$this->AssertEquals(1, $Store[0]);
		$this->AssertEquals(5, $Store[4]);

		// [ 42, 1, 2, 3, 4, 5 ]

		$Store->HeadPush(42);
		$this->AssertEquals(6, $Store->Count());
		$this->AssertEquals(42, $Store[0]);
		$this->AssertEquals(1, $Store[1]);

		// [ 1, 2, 3, 4, 5 ] -> 42

		$Val = $Store->HeadPop();
		$this->AssertEquals(42, $Val);
		$this->AssertEquals(5, $Store->Count());
		$this->AssertEquals(1, $Store[0]);

		return;
	}

	/** @test */
	public function
	TestTailMethodsAgainstLists():
	void {

		$Data = [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ];
		$Val = NULL;

		$Store = Common\Datastore::FromArray($Data);
		$this->AssertEquals(10, $Store->Count());

		// [ 6, 7, 8, 9, 10 ]

		$Store->TailCrop(5);
		$this->AssertEquals(5, $Store->Count());
		$this->AssertEquals(6, $Store[0]);
		$this->AssertEquals(10, $Store[4]);

		// [ 6, 7, 8, 9, 10, 42 ]

		$Store->TailPush(42);
		$this->AssertEquals(6, $Store->Count());
		$this->AssertEquals(10, $Store[4]);
		$this->AssertEquals(42, $Store[5]);

		// [ 6, 7, 8, 9, 10 ] -> 42

		$Val = $Store->TailPop();
		$this->AssertEquals(5, $Store->Count());
		$this->AssertEquals(42, $Val);
		$this->AssertEquals(10, $Store[4]);
		$this->AssertNull($Store[5]);

		return;
	}

	/** @test */
	public function
	TestIncBamps():
	void {

		$Store = new Common\Datastore([ 0, 0.0, 'string', FALSE, NULL ]);

		$this->AssertIsInt($Store[0]);
		$this->AssertIsFloat($Store[1]);
		$this->AssertIsString($Store[2]);
		$this->AssertIsBool($Store[3]);
		$this->AssertNull($Store[4]);

		// bamp

		$this->AssertEquals(1, $Store->Inc(0));
		$this->AssertIsInt($Store[0]);

		$this->AssertEquals(1.0, $Store->Inc(1));
		$this->AssertIsFloat($Store[1]);

		// big bamp

		$this->AssertEquals(4, $Store->Inc(0, 3));
		$this->AssertIsInt($Store[0]);

		$this->AssertEquals(4.0, $Store->Inc(1, 3));
		$this->AssertIsFloat($Store[1]);

		// back bamp

		$this->AssertEquals(3, $Store->Inc(0, -1));
		$this->AssertIsInt($Store[0]);

		$this->AssertEquals(3.0, $Store->Inc(1, -1));
		$this->AssertIsFloat($Store[1]);

		// fractional bamp

		$this->AssertEquals(4.2, $Store->Inc(0, 1.2));
		$this->AssertIsFloat($Store[0]);

		$this->AssertEquals(4.2, $Store->Inc(1, 1.2));
		$this->AssertIsFloat($Store[1]);

		return;
	}

	/** @test */
	public function
	TestBumpBamps():
	void {

		$Store = new Common\Datastore([ 0, 0.0, 'string', FALSE, NULL ]);

		$this->AssertIsInt($Store[0]);
		$this->AssertIsFloat($Store[1]);
		$this->AssertIsString($Store[2]);
		$this->AssertIsBool($Store[3]);
		$this->AssertNull($Store[4]);

		// bamp

		$Store->Bump(0);
		$this->AssertEquals(1, $Store[0]);
		$this->AssertIsInt($Store[0]);

		$Store->Bump(1);
		$this->AssertEquals(1.0, $Store[1]);
		$this->AssertIsFloat($Store[1]);

		// big bamp

		$Store->Bump(0, 3);
		$this->AssertEquals(4, $Store[0]);
		$this->AssertIsInt($Store[0]);

		$Store->Bump(1, 3);
		$this->AssertEquals(4.0, $Store[1]);
		$this->AssertIsFloat($Store[1]);

		// backwards bamp

		$Store->Bump(0, -1);
		$this->AssertEquals(3, $Store[0]);
		$this->AssertIsInt($Store[0]);

		$Store->Bump(1, -1);
		$this->AssertEquals(3.0, $Store[1]);
		$this->AssertIsFloat($Store[1]);

		// fractional bamp

		$Store->Bump(0, 1.2);
		$this->AssertEquals(4.2, $Store[0]);
		$this->AssertIsFloat($Store[0]);

		$Store->Bump(1, 1.2);
		$this->AssertEquals(4.2, $Store[1]);
		$this->AssertIsFloat($Store[1]);

		return;
	}

	/** @test */
	public function
	TestUniqueAndFlatten():
	void {

		$Store = new Common\Datastore([ 1, 2, 2, 3, 3, 3 ]);
		$Uniq = $Store->Unique();

		// check we got a new list of unique items that is separate from
		// the original datastore.

		$this->AssertEquals(6, $Store->Count());
		$this->AssertEquals(3, $Uniq->Count());

		$Store->Flatten();
		$this->AssertEquals(3, $Store->Count());
		$this->AssertEquals(3, $Uniq->Count());

		$Uniq->Clear();
		$this->AssertEquals(3, $Store->Count());
		$this->AssertEquals(0, $Uniq->Count());

		return;
	}

	/** @test */
	public function
	TestFlip():
	void {

		$Store = new Common\Datastore([ 'one', 'two', 'three' ]);
		$this->AssertFalse($Store->HasKey('one'));
		$this->AssertTrue($Store->HasValue('one'));

		$Store->Flip();
		$this->AssertTrue($Store->HasKey('one'));
		$this->AssertFalse($Store->HasValue('one'));
		$this->AssertEquals(0, $Store['one']);

		return;
	}

	/** @test */
	public function
	TestSliceChop():
	void {

		$Store = new Common\Datastore([ 1, 2, 3, 4 ]);

		$Slice = $Store->Slice(2);
		$this->AssertEquals(4, $Store->Count());
		$this->AssertEquals(2, $Slice->Count());
		$this->AssertEquals(3, $Slice[0]);

		$Slice = $Store->Slice(1, 2);
		$this->AssertEquals(4, $Store->Count());
		$this->AssertEquals(2, $Slice->Count());
		$this->AssertEquals(2, $Slice[0]);

		$Store->Chop(1, 2);
		$this->AssertEquals(2, $Store->Count());
		$this->AssertEquals(2, $Store[0]);

		return;
	}

	/** @test */
	public function
	TestInsertAfterList():
	void {

		$Data = Common\Datastore::FromArray([
			'zero', 'one', 'two', 'three'
		]);

		////////

		$this->AssertCount(4, $Data);

		////////

		$Data->InsertAfter(2, 'two and a half');

		$this->AssertCount(5, $Data);
		$this->AssertEquals('two and a half', $Data[3]);

		////////

		return;
	}

	/** @test */
	public function
	TestShoveAfterAssoc():
	void {

		$Data = Common\Datastore::FromArray([
			'k0'=> 'zero', 'k1'=> 'one', 'k2'=> 'two', 'k3'=> 'three'
		]);

		////////

		$this->AssertCount(4, $Data);

		////////

		$Data->ShoveAfter('k2', 'k25', 'two and a half');
		$this->AssertCount(5, $Data);
		$this->AssertEquals('two and a half', $Data['k25']);

		$Keys = array_keys($Data->Export());
		$this->AssertEquals('k2', $Keys[2]);
		$this->AssertEquals('k25', $Keys[3]);
		$this->AssertEquals('k3', $Keys[4]);

		////////

		return;
	}

	/** @test */
	public function
	TestCompile():
	void {

		$Data = new Common\Datastore([1, 2, 3, 4]);

		$More = $Data->Compile(
			fn(Common\Datastore $C, int $V)=> $C->Push($V)
		);

		$this->AssertCount(4, $More);

		$More->EachKeyValue(
			fn(int $K, int $V)
			=> $this->AssertEquals($More[$K], $Data[$K])
		);

		return;
	}

	/** @test */
	public function
	TestReverseMirror():
	void {

		$Data = new Common\Datastore([ 1, 2, 3, 4 ]);
		$Data->Reverse();

		$this->AssertEquals(4, $Data[0]);
		$this->AssertEquals(1, $Data[3]);

		$Atad = $Data->Mirror();

		$this->AssertTrue(spl_object_id($Data) !== spl_object_id($Atad));
		$this->AssertEquals(1, $Atad[0]);
		$this->AssertEquals(4, $Atad[3]);

		return;
	}

};
