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
	////////////////////////////////////////////////////////////////

	#[Common\Meta\Date('2023-11-21')]
	public function
	Is(string|self $Path):
	bool {

		if($Path instanceof static)
		$Path = $Path->Path;

		$Path = rtrim($Path, DIRECTORY_SEPARATOR);

		return $this->Path === $Path;
	}

	#[Common\Meta\Date('2023-11-21')]
	public function
	IsNot(string|self $Path):
	bool {

		return !$this->Is($Path);
	}

	#[Common\Meta\Date('2023-11-21')]
	public function
	Exists():
	bool {

		return (TRUE
			&& file_exists($this->Path)
			&& is_link($this->Path)
		);
	}

	#[Common\Meta\Date('2023-11-21')]
	public function
	Valid():
	bool {

		return (realpath($this->Source) !== FALSE);
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

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Common\Meta\Date('2023-11-21')]
	static public function
	FromPathSource(string $Path, string $Source):
	static {

		return new static([ 'Path'=> $Path, 'Source'=> $Source ]);
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Common\Meta\Date('2023-11-21')]
	static public function
	ForSortingByPath(self $A, self $B):
	int {

		return $A->Path <=> $B->Path;
	}

	#[Common\Meta\Date('2023-11-21')]
	static public function
	ForSortingBySource(self $A, self $B):
	int {

		return $A->Source <=> $B->Source;
	}

}
