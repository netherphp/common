<?php

namespace Nether\Common;
use Nether;

use Nether\Object\Datastore;

class Library {

	const
	ConfDefaultTimezone = 'Nether.Common.Date.Timezone';

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static Nether\Object\Datastore
	$Config;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(...$Argv) {

		static::$Config = $Argv['Config'] ?? new Nether\Object\Datastore;

		$this->OnLoad($Argv);

		return;
	}

	public function
	OnLoad(...$Argv):
	void {

		return;
	}

	public function
	OnPrepare(...$Argv):
	void {

		return;
	}

	public function
	OnReady(...$Argv):
	void {

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
