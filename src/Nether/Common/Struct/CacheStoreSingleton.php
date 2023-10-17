<?php

namespace Nether\Common\Struct;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Nether\Common;

abstract class CacheStoreSingleton {

	static protected Common\Datastore
	$Data;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	Init():
	void {

		if(isset(static::$Data))
		return;

		static::$Data = new Common\Datastore;

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	Drop(string $Key):
	void {

		static::Init();

		static::$Data->Remove($Key);

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

		if(!isset(static::$Data))
		return FALSE;

		return static::$Data->HasKey($Key);
	}

	static public function
	Set(string $Key, string $Val):
	void {

		static::Init();

		if(static::$Data->HasKey($Key))
		static::$Data->Set($Key, $Val);

		return;
	}

}
