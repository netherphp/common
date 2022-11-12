<?php

namespace Nether\Common;
use Nether;

use Nether\Object\Datastore;

abstract class Library {

	static Nether\Object\Datastore
	$Config;

	static public function
	PrepareDefaultConfig(?Datastore $Config=NULL):
	Datastore {
	/*//
	provide a base implementation for setting library values to their
	default during initialisation. this default version will create a new
	datastore if one was not given, store it the static storage, and return
	it too just in case. if you are overriding this to provide defaults for
	a lib it is suggested you call this parent version first just to get the
	magic consistent storage in the global static.
	//*/

		// if we were not given a persistent configuration then make our
		// own with blackhack and hookers.

		if($Config === NULL)
		$Config = new Datastore;

		// and have the library remember the config that was used.

		static::$Config = $Config;

		// then spit it out for whatever reason too why not.

		return $Config;
	}

}
