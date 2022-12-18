<?php

namespace Nether\Common;
use Nether;

use Nether\Object\Datastore;

class Library {

	static Nether\Object\Datastore
	$Config;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(...$Argv) {

		static::$Config = (
			$Argv['Config']
			?? new Nether\Object\Datastore
		);

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

}
