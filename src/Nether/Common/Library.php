<?php

namespace Nether\Common;
use Nether;

use Nether\Common\Datastore;

class Library {

	static Nether\Common\Datastore
	$Config;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(...$Argv) {

		static::$Config = $Argv['Config'] ?? new Nether\Common\Datastore;

		$this->OnLoad(...$Argv);

		return;
	}

	public function
	OnLoad(...$Argv):
	void {
	/*//
	when a library is first require'd from disk. the library may self
	configure any defaults here, but know that the framework that
	loaded it may not be fully configured itself yet.
	//*/

		return;
	}

	public function
	OnPrepare(...$Argv):
	void {
	/*//
	after the framework that loaded this has determined the environment
	and its required setup.
	//*/

		return;
	}

	public function
	OnReady(...$Argv):
	void {
	/*//
	after all libraries should have already configured themselves via load
	and prepare. last step before handing execution off to the application
	trying to use this stuff.
	//*/

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	// public api for library config object for considering making the
	// actual static property protected.

	static public function
	Config():
	Datastore {

		return static::$Config;
	}

	static public function
	Get(string $Key):
	mixed {

		return static::$Config[$Key];
	}

	static public function
	Set(string $Key, mixed $Val):
	mixed {

		return static::$Config[$Key] = $Val;
	}

	static public function
	Has(string $Key, bool $NullBeFine=FALSE):
	bool {

		// if the key does not exist then its a fail no matter what as we
		// clearly cannot has it.

		if(!static::$Config->HasKey($Key))
		return FALSE;

		// if the key is set to null that is a fail unless we are cool
		// with having nulls around.

		if(static::$Config[$Key] === NULL && !$NullBeFine)
		return FALSE;

		// else we have it, and its *something* so that is all we wanted
		// to know.

		return TRUE;
	}

}
