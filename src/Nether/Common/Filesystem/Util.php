<?php

namespace Nether\Common\Filesystem;

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
		$Arg = trim($Arg, $DS);

		return join($DS, $Argv);
	}

	static public function
	Repath(string $Input):
	string {

		if(PHP_OS_FAMILY === 'Windows')
		$Input = str_replace('/', '\\', $Input);

		return $Input;
	}

}
