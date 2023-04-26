<?php

namespace Nether\Common\Filesystem;

use Nether\Common;

use Exception;
use SplFileInfo;

class Util {

	static public function
	MkDir(string $Path):
	bool {

		$Path = self::Repath($Path);

		if(!file_exists($Path)) {
			$UMask = umask(0);
			mkdir($Path, 0777, TRUE);
			umask($UMask);
		}

		return is_dir($Path);
	}

	static public function
	MkTempFile(?string $Prefix='tmp', ?string $Path=NULL):
	string {

		$Path ??= sys_get_temp_dir();

		if(!is_writable($Path))
		throw new Common\Error\DirUnwritable($Path);

		// was having a tough time coming up with a method that could
		// purposely cause tempnam to fail since it auto falls back to
		// system dir when you give it trash. so we bail if the path is
		// failpath and keep fingers crossed that tempnam will be fine.

		$Filename = tempnam($Path, "{$Prefix}-");

		return $Filename;
	}

	static public function
	RmDir(string $Path):
	void {

		if(!is_dir($Path))
		throw new Common\Error\DirNotFound($Path);

		////////

		$Scan = new Indexer($Path);
		$Info = NULL;

		foreach($Scan as $Info) {
			/** @var SplFileInfo $Info */

			if($Info->IsDir()) {
				static::RmDir($Info->GetPathname());
				continue;
			}

			unlink($Info->GetPathname());
		}

		rmdir($Path);

		return;
	}

	static public function
	Chmod(string $Path, int $Mode):
	void {

		$UMask = umask(0);
		chmod($Path, $Mode);
		umask($UMask);

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	Pathify(...$Argv):
	string {

		return static::PathifyWith(DIRECTORY_SEPARATOR, ...$Argv);
	}

	static public function
	PathifyWith(string $DS, ...$Argv):
	string {

		$Arg = NULL;

		foreach($Argv as &$Arg)
		$Arg = rtrim($Arg, $DS);

		return join($DS, $Argv);
	}

	static public function
	Repath(string $Input):
	string {

		return static::RepathFor(PHP_OS_FAMILY, $Input);
	}

	static public function
	RepathFor(string $OS, string $Input):
	string {

		// windows does not allow forward slash in filenames. so any
		// forward slashes can be assumed to be directory separators.

		// linux does allow backslash in filenames. so converting any
		// backslashes into forward slashes would not really be a promise
		// of having a good path after.

		if($OS === 'Windows')
		$Input = str_replace('/', '\\', $Input);

		return $Input;
	}

}
