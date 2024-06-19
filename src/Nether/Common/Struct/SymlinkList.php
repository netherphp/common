<?php

namespace Nether\Common\Struct;

use Nether\Common;

class SymlinkList
extends Common\Datastore
implements
	Common\Interfaces\ToArray,
	Common\Interfaces\ToJSON {

	use
	Common\Package\ToJSON;

	////////////////////////////////////////////////////////////////
	// IMPLEMENTS Common\Datastore /////////////////////////////////

	protected function
	OnPrepare():
	void {

		$this->Rehydrate();

		return;
	}

	////////////////////////////////////////////////////////////////
	// IMPLEMENTS Common\Interfaces\ToArray ////////////////////////

	public function
	ToArray():
	array {

		return $this->Revalue()->Export();
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Rehydrate():
	static {

		$Key = NULL;
		$Val = NULL;

		foreach($this->Data as $Key => $Val) {
			if($Val instanceof Common\Filesystem\Symlink)
			continue;

			if(!is_iterable($Val)) {
				unset($this->Data[$Key]);
				continue;
			}

			$this->Data[$Key] = new Common\Filesystem\Symlink($Val);
		}

		$this->Revalue();

		return $this;
	}


};
