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

		$this->AssertTrue(isset($PStore['One']));
		$this->AssertFalse(isset($PStore['Zed']));

		$this->AssertEquals('Uno', $PStore['One']);
		$this->AssertNull($PStore['Zed']);

		$PStore['Banana'] = 'Yellow';
		$this->AssertTrue(isset($PStore['Banana']));
		$this->AssertEquals('Yellow', $PStore['Banana']);

		unset($PStore['Banana']);
		$this->AssertFalse(isset($PStore['Banana']));

		return;
	}

};
