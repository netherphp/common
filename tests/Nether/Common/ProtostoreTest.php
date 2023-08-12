<?php

namespace Nether\Common;

use PHPUnit\Framework\TestCase;

class ProtostoreTest
extends TestCase {

	/** @test */
	public function
	TestBasic():
	void {

		// test empty set.

		$Store = new Protostore;
		$this->AssertEquals(0, $Store->Count());
		$this->AssertFalse($Store->HasKey('One'));
		$this->AssertFalse($Store->HasKey('Two'));
		$this->AssertFalse($Store->HasKey('Three'));
		$this->AssertFalse($Store->HasValue(1));
		$this->AssertFalse($Store->HasValue(2));
		$this->AssertFalse($Store->HasValue(3));
		$this->AssertNull($Store->Get('One'));
		$this->AssertNull($Store->Get('Two'));
		$this->AssertNull($Store->Get('Three'));

		$this->AssertTrue(is_array($Store->GetData()));
		$this->AssertEquals(0, count($Store->GetData()));
		$this->AssertTrue($Store->GetDatastore() instanceof Datastore);
		$this->AssertEquals(0, $Store->GetDatastore()->Count());

		$this->AssertTrue(is_array($Store->Keys()));
		$this->AssertEquals(0, count($Store->Keys()));

		// test prefab set.

		$Store = new Protostore([ 'One'=> 1, 'Two'=> 2 ]);
		$this->AssertEquals(2, $Store->Count());
		$this->AssertTrue($Store->HasKey('One'));
		$this->AssertTrue($Store->HasKey('Two'));
		$this->AssertFalse($Store->HasKey('Three'));
		$this->AssertTrue($Store->HasValue(1));
		$this->AssertTrue($Store->HasValue(2));
		$this->AssertFalse($Store->HasValue(3));
		$this->AssertEquals(1, $Store->Get('One'));
		$this->AssertEquals(2, $Store->Get('Two'));
		$this->AssertNull($Store->Get('Three'));

		$Store->Set('Three', 3);
		$this->AssertTrue($Store->HasKey('Three'));
		$this->AssertTrue($Store->HasValue(3));
		$this->AssertEquals(3, $Store->Get('Three'));

		$Store->Unset('Three');
		$this->AssertFalse($Store->HasKey('Three'));
		$this->AssertFalse($Store->HasValue(3));
		$this->AssertNull($Store->Get('Three'));

		$this->AssertTrue(is_array($Store->GetData()));
		$this->AssertEquals(2, count($Store->GetData()));
		$this->AssertTrue($Store->GetDatastore() instanceof Datastore);
		$this->AssertEquals(2, $Store->GetDatastore()->Count());

		$this->AssertTrue(is_array($Store->Keys()));
		$this->AssertEquals(2, count($Store->Keys()));
		$this->AssertEquals('One', $Store->Keys()[0]);
		$this->AssertEquals('Two', $Store->Keys()[1]);

		return;
	}

	/** @test */
	public function
	TestDescribeForPublicAPI():
	void {

		$Store = new Protostore([ 'One'=> 1, 'Two'=> 2 ]);
		$Obj = $Store->DescribeForPublicAPI();

		$this->AssertTrue(is_object($Obj));
		$this->AssertTrue(property_exists($Obj, 'One'));
		$this->AssertTrue(property_exists($Obj, 'Two'));
		$this->AssertFalse(property_exists($Obj, 'Three'));
		$this->AssertEquals(1, $Obj->One);
		$this->AssertEquals(2, $Obj->Two);

		return;
	}

	/** @test */
	public function
	TestFilterDistiller():
	void {

		$Store = new Protostore([ 'One'=> 1, 'Two'=> 2 ]);
		$Result = $Store->Filter(fn(int $V)=> $V < 2);

		// see that it worked.
		$this->AssertEquals(1, $Result->Count());
		$this->AssertEquals(1, $Result->Get('One'));
		$this->AssertNull($Result->Get('Two'));
		$this->AssertNull($Result->Get('Three'));

		// without screwing with the original.
		$this->AssertEquals(2, $Store->Count());
		$this->AssertEquals(1, $Store->Get('One'));
		$this->AssertEquals(2, $Store->Get('Two'));
		$this->AssertNull($Store->Get('Three'));

		return;
	}

	/** @test */
	public function
	TestFromArray():
	void {

		$Store = Protostore::FromArray([ 'One'=> 1, 'Two'=> 2 ]);

		$this->AssertEquals(2, $Store->Count());
		$this->AssertEquals(1, $Store->Get('One'));
		$this->AssertEquals(2, $Store->Get('Two'));
		$this->AssertNull($Store->Get('Three'));

		return;
	}

	/** @test */
	public function
	TestFromJSON():
	void {

		$Store = Protostore::FromJSON('{ "One": 1, "Two": 2 }');
		$this->AssertEquals(2, $Store->Count());
		$this->AssertEquals(1, $Store->Get('One'));
		$this->AssertEquals(2, $Store->Get('Two'));
		$this->AssertNull($Store->Get('Three'));

		$Store = Protostore::FromJSON(NULL);
		$this->AssertEquals(0, $Store->Count());
		$this->AssertNull($Store->Get('One'));
		$this->AssertNull($Store->Get('Two'));
		$this->AssertNull($Store->Get('Three'));

		$Store = Protostore::FromJSON('oh snap');
		$this->AssertEquals(0, $Store->Count());
		$this->AssertNull($Store->Get('One'));
		$this->AssertNull($Store->Get('Two'));
		$this->AssertNull($Store->Get('Three'));


		return;
	}

}