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

	public function
	TestIterator():
	void {

		$Data = [ 'One'=> 'Uno', 'Two'=> 'Dos', 'Three'=> 'Tres' ];
		$PStore = new Protostore($Data);

		// current
		$this->AssertEquals('Uno', current($PStore));

		// key
		$this->AssertEquals('One', key($PStore));

		// next
		next($PStore);
		$this->AssertEquals('Dos', current($PStore));
		$this->AssertEquals('Two', key($PStore));

		// rewind
		rewind($PStore);
		$this->AssertEquals('Uno', current($PStore));
		$this->AssertEquals('One', key($PStore));

		return;
	}

};
