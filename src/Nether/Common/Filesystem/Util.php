<?php

namespace Nether\Common\Filesystem;

use Exception;
use SplFileInfo;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

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

		if(PHP_OS_FAMILY === 'Windows')
		$Input = str_replace('/', '\\', $Input);

		return $Input;
	}

	static public function
	RmDir(string $Path):
	void {

		if(!is_dir($Path))
		throw new Exception("{$Path} is not a directory.");

		////////

		$Scan = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($Path, (
				0
				| RecursiveDirectoryIterator::SKIP_DOTS
				| RecursiveDirectoryIterator::CURRENT_AS_FILEINFO
			))
		);

		$Info = NULL;

		foreach($Scan as $Info) {
			/** @var SplFileInfo $Info */

			if($Info->IsDir()) {
				static::RmDir($Info->GetPathname());
				rmdir($Info->GetPathname());
				continue;
			}

			unlink($Info->GetPathname());
		}

		rmdir($Path);

		return;
	}

}
