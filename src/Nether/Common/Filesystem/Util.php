<?php ##########################################################################
################################################################################

namespace Nether\Common\Filesystem;

use Nether\Common;
use FileEye;

use Exception;
use SplFileInfo;

################################################################################
################################################################################

class Util {

	#[Common\Meta\Date('2023-10-31')]
	#[Common\Meta\Info('Make the supplied file path vanish without having to care what it is.')]
	static public function
	Delete(string $Path):
	void {

		if(is_file($Path)) {
			unlink($Path);
			return;
		}

		if(is_dir($Path)) {
			static::RmDir($Path);
			return;
		}

		if(file_exists($Path))
		throw new Exception("Failed to delete {$Path}");

		return;
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
			continue;
		}

		rmdir($Path);

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	MkDir(string $Path, int $Mode=0777):
	bool {

		$Path = self::Repath($Path);
		$Mode ??= 0777;

		if(!file_exists($Path)) {
			$UMask = umask(0);
			mkdir($Path, $Mode, TRUE);
			umask($UMask);
		}

		else {
			static::Chmod($Path, $Mode);
			// var_dump(decoct(fileperms($Path)));
		}

		return is_dir($Path);
	}

	static public function
	MkTempFile(?string $Prefix='nt', ?string $Path=NULL):
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
	IsLinkTo(string $Link, string $Source):
	bool {

		if(is_link($Link))
		if(readlink($Link) === $Source)
		return TRUE;

		////////

		return FALSE;
	}

	static public function
	IsAbsolutePath(string $Path):
	bool {

		return static::IsAbsolutePathFor(PHP_OS_FAMILY, $Path);
	}

	static public function
	IsAbsolutePathFor(string $OS, string $Path):
	bool {

		if($OS === 'Windows')
		return (bool)preg_match('#^([A-Za-z]:){0,1}\\\\#', $Path);

		return str_starts_with($Path, static::RepathFor($OS, '/'));
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
		$Arg = rtrim($Arg ?? '', $DS);

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

	static public function
	ReplaceFileExtension(string $Input, string $ExtNew, string $ExtDelim='.', int $ExtDepth=1):
	string {

		$Path = dirname($Input);
		$Base = basename($Input);

		// extensionless files gain one.
		// whatever -> whatever.txt

		if(!str_contains($Base, $ExtDelim))
		$Base = sprintf('%s%s%s', $Base, $ExtDelim, $ExtNew);

		// extensioned files get theirs swapped.
		// whatever.txt -> whatever.md

		else
		$Base = substr_replace(
			$Base,
			sprintf('%s%s', $ExtDelim, $ExtNew),
			strrpos($Base, $ExtDelim)
		);

		////////

		if(!$Path || $Path === '.')
		return $Base;

		////////

		return Common\Filesystem\Util::Pathify($Path, $Base);
	}

	static public function
	Basename(string $Input, int $Len=1):
	string {

		$Bits = explode(DIRECTORY_SEPARATOR, $Input);

		if(count($Bits) < $Len)
		return join(DIRECTORY_SEPARATOR, $Bits);

		return join(
			DIRECTORY_SEPARATOR,
			array_slice($Bits, (count($Bits) - $Len))
		);
	}

	static public function
	Prechomp(string $BaseDir, string $Path):
	string {

		return trim(
			str_replace($BaseDir, '', $Path),
			DIRECTORY_SEPARATOR
		);
	}

	static public function
	Prefix(string $BaseDir, string $Path):
	string {

		return static::Pathify($BaseDir, $Path);
	}

	static public function
	LineCount(string $Filename):
	int {

		$Count = 0;
		$FP = fopen($Filename, 'r');
		$Chunk = NULL;

		if(!$FP)
		throw new Common\Error\FileNotFound($Filename);

		////////

		while($Chunk = fread($FP, 2048))
		$Count += substr_count($Chunk, "\n");

		fclose($FP);

		////////

		return $Count;
	}

	static public function
	Filesize(string $Filename):
	int {

		clearstatcache($Filename);

		return filesize($Filename);
	}

	#[Common\Date('2024-06-21')]
	static public function
	MimeType(string $Filename):
	string {

		$Type = mime_content_type($Filename);

		////////

		if($Type)
		return $Type;

		return 'application/octet-stream';
	}

	static public function
	TryToReadFile(string $Filename):
	string {

		if(!file_exists($Filename))
		throw new Common\Error\FileNotFound($Filename);

		if(!is_readable($Filename))
		throw new Common\Error\FileUnreadable($Filename);

		////////

		$Data = file_get_contents($Filename);

		if($Data === FALSE)
		throw new Common\Error\FileReadError($Filename);

		////////

		return $Data;
	}

	static public function
	TryToReadFileJSON(string $Filename):
	mixed {

		$JSON = static::TryToReadFile($Filename);
		$Data = json_decode($JSON, TRUE);

		////////

		// @NOTE 2024-04-13
		// ever stop in awe of how thread unsafe this is?

		if(json_last_error() !== JSON_ERROR_NONE)
		throw new Common\Error\FormatInvalidJSON;

		////////

		return $Data;
	}

	static public function
	TryToWriteFile(string $Filename, mixed $Data):
	int {

		$Exists = file_exists($Filename);
		$Dirname = dirname($Filename);

		////////

		if($Exists && !is_writable($Filename))
		throw new Common\Error\FileUnwritable($Filename);

		if(!$Exists) {
			if(!is_dir($Dirname))
			static::MkDir($Dirname);

			if(!is_dir($Dirname))
			throw new Common\Error\DirNotFound($Dirname);

			if(!is_writable($Dirname))
			throw new Common\Error\DirUnwritable($Dirname);
		}

		////////

		$Bytes = file_put_contents($Filename, $Data);

		if($Bytes === FALSE)
		throw new Common\Error\FileWriteError($Filename);

		////////

		return $Bytes;
	}

}
