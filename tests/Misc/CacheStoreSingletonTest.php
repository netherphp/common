<?php

namespace NetherTestSuite\Misc\CacheStoreSingleton;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Nether\Common;
use PHPUnit;

use Exception;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class Cache0
extends Common\Struct\CacheStoreSingleton {

};

class Cache1
extends Common\Struct\CacheStoreSingleton {

	static protected Common\Datastore
	$Data;

};

class Cache2
extends Common\Struct\CacheStoreSingleton {

	static protected Common\Datastore
	$Data;

};

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class CacheStoreSingletonTest
extends PHPUnit\Framework\TestCase {

	/** @test */
	public function
	TestExtensionDefineDataProprerty():
	void {

		// for this to work as expected where each extension of this class
		// mainains its own datastore the child classes must define the
		// property themselves, else they will all feed from the copy of
		// the object on the parent class.

		$Exceptional = FALSE;

		////////

		try { $this->AssertEquals(0, Cache0::Count()); }
		catch(Exception $Err) { $Exceptional = TRUE; }

		$this->AssertTrue($Exceptional);
		$this->AssertInstanceOf(Common\Error\RequiredDataMissing::class, $Err);

		return;
	}

	/** @test */
	public function
	TestDuelingBanjos():
	void {


		$this->AssertEquals(0, Cache1::Count());
		$this->AssertEquals(0, Cache2::Count());

		////////

		Cache1::Set('Thing', 'Thing');
		Cache2::Set('Thang', 'Thang');

		$this->AssertEquals(1, Cache1::Count());
		$this->AssertEquals(1, Cache2::Count());

		$this->AssertFalse(Cache1::Has('Thang'));
		$this->AssertFalse(Cache2::Has('Thing'));

		return;
	}

}
