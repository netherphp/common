<?php

namespace Nether\Common\Struct;

use Nether\Common;

class CommandItem
extends Common\Prototype
implements
	Common\Interfaces\ToArray,
	Common\Interfaces\ToJSON {

	use
	Common\Package\ToJSON;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public ?string
	$Name = NULL;

	#[Common\Meta\PropertyFactory('FromArray')]
	public array|Common\Datastore
	$Steps = [];

	////////////////////////////////////////////////////////////////
	// IMPLEMENTS Common\Interfaces\ToArray ////////////////////////

	public function
	ToArray():
	array {

		$Output = [
			'Name'  => $this->Name,
			'Steps' => $this->Steps->GetData()
		];

		return $Output;
	}

};
