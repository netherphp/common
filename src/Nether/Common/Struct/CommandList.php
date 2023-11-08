<?php

namespace Nether\Common\Struct;

use Nether\Common;

class CommandList
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

		$this->Commandify();

		return;
	}

	////////////////////////////////////////////////////////////////
	// IMPLEMENTS Common\Interfaces\ToArray ////////////////////////

	public function
	ToArray():
	array {

		// the name property gets set by the key of the command the
		// user does not really need to specify that again unless they
		// really want to or have a reason it should be different.

		$Output = array_map(
			function(CommandItem $D){
				$A = $D->ToArray();
				unset($A['Name']);

				return $A;
			},
			$this->GetData()
		);

		return $Output;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Commandify():
	static {

		$Key = NULL;
		$Val = NULL;

		foreach($this->Data as $Key => $Val) {
			if($Val instanceof CommandItem)
			continue;

			if(!is_iterable($Val)) {
				unset($this->Data[$Key]);
				continue;
			}

			$this->Data[$Key] = new CommandItem($Val);

			if($this->Data[$Key]->Name === NULL && !is_numeric($Key))
			$this->Data[$Key]->Name = $Key;
		}

		return $this;
	}

};
