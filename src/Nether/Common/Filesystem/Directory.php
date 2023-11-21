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

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Is(string|self $Path):
	bool {

		if($Path instanceof static)
		$Path = $Path->Path;

		$Path = rtrim($Path, DIRECTORY_SEPARATOR);

		return $this->Path === $Path;
	}

	public function
	IsNot(string|self $Path):
	bool {

		return !$this->Is($Path);
	}

	public function
	Exists():
	bool {

		return (
			TRUE
			&& file_exists($this->Path)
			&& is_dir($this->Path)
		);
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	FromPath(string $Path):
	static {

		return new static([ 'Path'=> $Path ]);
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	ForSortingByPath(self $A, self $B):
	int {

		return $A->Path <=> $B->Path;
	}

}
