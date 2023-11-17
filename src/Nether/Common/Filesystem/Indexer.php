<?php

namespace Nether\Common\Filesystem;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Nether\Common;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use IteratorIterator;
use FilesystemIterator;
use FilterIterator;
use SplFileInfo;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class Indexer
extends FilterIterator {

	public function
	__Construct(string $Dir, bool $Recur=FALSE) {

		$Flags = (
			0
			| FilesystemIterator::SKIP_DOTS
			| FilesystemIterator::CURRENT_AS_FILEINFO
		);

		////////

		if($Recur)
		$Outermost = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($Dir, $Flags)
		);

		else
		$Outermost = new IteratorIterator(
			new FilesystemIterator($Dir, $Flags)
		);

		////////

		parent::__Construct($Outermost);
		return;
	}

	public function
	Accept():
	bool {

		return TRUE;
	}

	public function
	ToArray(bool $SplFileInfo=FALSE):
	array {

		$File = NULL;
		$Output = [];

		foreach($this as $File) {
			/** @var SplFileInfo $File */

			$Output[] = match(TRUE) {
				$SplFileInfo
				=> $File,

				default
				=> $File->GetRealPath()
			};
		}

		return $Output;
	}

	public function
	ToDatastore(bool $SplFileInfo=FALSE):
	Common\Datastore {

		return Common\Datastore::FromArray($this->ToArray($SplFileInfo));
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	FromPath(string $Dir, bool $Recur=FALSE):
	static {

		return new static($Dir, $Recur);
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	ArrayFromPath(string $Dir, bool $Recur=FALSE, bool $SplFileInfo=FALSE):
	array {

		return static::FromPath($Dir, $Recur)->ToArray($SplFileInfo);
	}

	static public function
	DatastoreFromPath(string $Dir, bool $Recur=FALSE, bool $SplFileInfo=FALSE):
	Common\Datastore {

		return static::FromPath($Dir, $Recur)->ToDatastore($SplFileInfo);
	}

}
