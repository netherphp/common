<?php

namespace Nether\Common\Struct;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Nether\Common;

abstract class CacheStoreSingleton {

	// this must be defined by the child class for the effect to work as
	// expected, where they each maintain their own datastores per class.
	// else they all end up pointing to the parent copy.

	// static protected Common\Datastore
	// $Data;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	Init():
	void {

		if(!property_exists(static::class, 'Data'))
		throw new Common\Error\RequiredDataMissing(
			'Data',
			'Extensions of CacheStoreSingleton must define $Data property.'
		);

		////////

		if(isset(static::$Data))
		return;

		static::$Data = new Common\Datastore;

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	Count():
	int {

		static::Init();

		return static::$Data->Count();
	}

	static public function
	Drop(string $Key):
	void {

		static::Init();

		static::$Data->Remove($Key);

		return;
	}

	static public function
	Flush():
	void {

		static::Init();

		static::$Data->Clear();
		return;
	}

	static public function
	Get(string $Key):
	mixed {

		static::Init();

		return static::$Data->Get($Key);
	}

	static public function
	Has(string $Key):
	bool {

		static::Init();

		return static::$Data->HasKey($Key);
	}

	static public function
	Set(string $Key, string $Val):
	void {

		static::Init();

		static::$Data->Set($Key, $Val);

		return;
	}

}
