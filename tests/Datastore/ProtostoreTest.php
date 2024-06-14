<?php

namespace NetherTestSuite\Common\Datastore;

use PHPUnit\Framework\TestCase;
use Nether\Common\Protostore;

class ProtostoreTest
extends TestCase {

	/** @test */
	public function
	TestArrayAccess():
	void {

		$Data = [ 'One'=> 'Uno', 'Two'=> 'Dos', 'Three'=> 'Tres' ];
		$PStore = new Protostore($Data);

		// offset exists
		$this->AssertTrue(isset($PStore['One']));
		$this->AssertFalse(isset($PStore['Zed']));

		// offset get
		$this->AssertEquals('Uno', $PStore['One']);
		$this->AssertNull($PStore['Zed']);

		// offset set
		$PStore['Banana'] = 'Yellow';
		$this->AssertTrue(isset($PStore['Banana']));
		$this->AssertEquals('Yellow', $PStore['Banana']);

		// offset unset
		unset($PStore['Banana']);
		$this->AssertFalse(isset($PStore['Banana']));

		return;
	}

	/** @test */
	public function
	TestIterator():
	void {

		$Data = [ 'One'=> 'Uno', 'Two'=> 'Dos', 'Three'=> 'Tres' ];
		$PStore = new Protostore($Data);
		$Item = NULL;

		// so i was surprised to find that implementing these things for
		// Iterator isn't making current(), key(), and next() functions
		// work as expected, like current($PStore);

		// current

		$this->AssertEquals('Uno', $PStore->Current());

		// key

		$this->AssertEquals('One', $PStore->Key());

		// next

		$PStore->Next();
		$this->AssertEquals('Dos', $PStore->Current());
		$this->AssertEquals('Two', $PStore->Key());

		// rewind

		$PStore->Rewind();
		$this->AssertEquals('Uno', $PStore->Current());
		$this->AssertEquals('One', $PStore->Key());

		// valid

		$this->AssertTrue($PStore->Valid());
		foreach($PStore as $Item) { }

		$this->AssertFalse($PStore->Valid());

		$PStore->Rewind();
		$this->AssertTrue($PStore->Valid());

		return;
	}

};
