<?php

namespace Nether\Common\Filesystem;

use Nether\Common;

use JsonSerializable;

class File
extends Common\Prototype
implements
	Common\Interfaces\ToArray,
	Common\Interfaces\ToJSON,
	JsonSerializable {

	use
	Common\Package\ToJSON,
	Common\Package\JsonSerializableAsToJSON;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

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
	// IMPLEMENTS Common Interfaces ////////////////////////////////

	public function
	ToArray():
	array {

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
			&& is_file($this->Path)
		);
	}

	public function
	Write(string $Data):
	static {

		if(!file_exists($this->Path))
		if(!is_writable(dirname($this->Path)))
		throw new Common\Error\DirUnwritable($this->Path);

		if(file_exists($this->Path))
		if(!is_writable($this->Path))
		throw new Common\Error\FileUnwritable($this->Path);

		////////

		file_put_contents($this->Path, $Data);

		return $this;
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
