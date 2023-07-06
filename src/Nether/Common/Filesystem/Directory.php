<?php

namespace Nether\Common\Filesystem;

use Nether\Common;

use JsonSerializable;

class Directory
extends Common\Prototype
implements JsonSerializable {

	public string
	$Path;

	public int|string
	$Mode = 0o777;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected function
	OnReady(Common\Prototype\ConstructArgs $Args):
	void {

		if(is_string($this->Mode))
		$this->Mode = Common\Filters\Numbers::IntFromNumeric($this->Mode);

		return;
	}

	////////////////////////////////////////////////////////////////
	// implements JsonSerializable /////////////////////////////////

	public function
	JsonSerialize():
	mixed {

		return [
			'Path' => $this->Path,
			'Mode' => sprintf('0o%o', $this->Mode)
		];
	}

}
