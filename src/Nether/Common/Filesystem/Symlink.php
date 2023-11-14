<?php

namespace Nether\Common\Filesystem;

use Nether\Common;

use JsonSerializable;

// this is meant as like a super high level description of what a symlink must
// consist of. this will likely only be used in config files.

class Symlink
extends Common\Prototype
implements JsonSerializable {

	public string
	$Path;

	public string
	$Source;

	public string|int
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
			'Path'   => $this->Path,
			'Source' => $this->Source,
			'Mode'   => sprintf('0o%o', $this->Mode)
		];
	}

}
