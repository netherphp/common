<?php

namespace Nether\Common\Phar;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Nether\Common;

use Phar;
use RecursiveIteratorIterator;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class Builder
extends Common\Prototype {

	public string
	$OutputFile;

	public string
	$Bin;

	public Common\Datastore
	$Files;

	public Common\Datastore
	$FileFilters;

	public ?string
	$BaseDir;

	public ?string
	$BuildDir;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	SetOutputFile(string $File):
	static {

		$this->OutputFile = $File;

		return $this;
	}

	public function
	SetBin(string $Filepath):
	static {

		$this->Bin = $Filepath;

		return $this;
	}

	public function
	SetFileList(Common\Datastore $Files):
	static {

		$this->Files = $Files;

		return $this;
	}

	public function
	SetBaseDir(?string $Dir):
	static {

		$this->BaseDir = $Dir;

		return $this;
	}

	public function
	SetBuildDir(?string $Dir):
	static {

		$this->BuildDir = $Dir;

		return $this;
	}

	public function
	SetFileFilters(iterable $Input):
	static {

		$this->FileFilters = new Common\Datastore($Input);

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Build():
	void {

		$this->PrepareForBuild();
		$this->DeleteFilesInBuild();
		$this->CopyFilesIntoBuild();
		$this->CompilePharOfBuild();
		$this->InspectPharOfBuild();

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	PrepareForBuild():
	void {

		if(!$this->BaseDir)
		$this->BaseDir = $this->GetDefaultBaseDir();

		if(!$this->BaseDir)
		throw new Common\Error\RequiredDataMissing('BaseDir', 'string<filepath>');

		////////

		if(!$this->BuildDir)
		$this->BuildDir = $this->GetDefaultBuildDir();

		if(!$this->BuildDir)
		throw new Common\Error\RequiredDataMissing('BuildDir', 'string<filepath>');

		if(file_exists($this->BuildDir)) {
			if(is_file($this->BuildDir))
			throw new Common\Error\FileUnwritable($this->BuildDir);

			if(!is_writable($this->BuildDir))
			throw new Common\Error\DirUnwritable($this->BuildDir);
		}

		if(!Common\Filesystem\Util::MkDir($this->BuildDir))
		throw new Common\Error\DirUnwritable(dirname($this->BuildDir));

		////////

		if(!isset($this->Bin))
		throw new Common\Error\RequiredDataMissing('Bin', 'string<filepath>');

		////////

		$this->ExpandFilesToBuild();

		return;
	}

	public function
	ExpandFilesToBuild():
	void {

		$Files = new Common\Datastore;
		$File = NULL;
		$Path = NULL;
		$Indexer = NULL;

		// using what we know run thorugh the provided list of files and
		// expand any of them that were directories into full indexes of
		// those directories.

		foreach($this->Files as $File) {
			$Path = Common\Filesystem\Util::Pathify($this->BaseDir, $File);

			if(is_dir($Path)) {
				$Indexer = new Common\Filesystem\Indexer($Path, TRUE);

				$Files->MergeRight(
					($Indexer->ToDatastore())
					->Remap(
						fn(string $P)
						=> trim(str_replace($this->BaseDir, '', $P), DIRECTORY_SEPARATOR)
					)
				);

				$Indexer = NULL;
				continue;
			}

			$Files->Push($File);
			continue;
		}

		// then apply any filters to the list now that we really know
		// everything that needs to go in. filters should expect a single
		// string argument to inspect.

		if(isset($this->FileFilters))
		$this->FileFilters->Each(fn(callable $Fn)=> $Files->Filter($Fn));

		$this->Files = $Files;

		return;
	}

	public function
	CopyFilesIntoBuild():
	void {

		$BuildDir = $this->BuildDir;
		$Paths = $this->GetFileCopyMap();
		$From = NULL;
		$Dest = NULL;
		$DestDir = NULL;

		printf(
			'Copying %d file(s) into %s.%s%s',
			$Paths->Count(), $BuildDir, PHP_EOL, PHP_EOL
		);

		foreach($Paths as $From => $Dest) {
			//printf('+ %s%s', $From, PHP_EOL);
			//printf('  %s%s%s', $Dest, PHP_EOL, PHP_EOL);

			if(is_file($From)) {
				$DestDir = dirname($Dest);

				if(!is_dir($DestDir))
				Common\Filesystem\Util::MkDir($DestDir);

				copy($From, $Dest);
				continue;
			}

			if(is_dir($From)) {

				continue;
			}
		}

		return;
	}

	public function
	IndexFilesInBuild():
	Common\Datastore {

		$Indexer = new Common\Filesystem\Indexer($this->BuildDir, TRUE);

		return $Indexer->ToDatastore();
	}

	public function
	DeleteFilesInBuild():
	void {

		$Indexer = new Common\Filesystem\Indexer($this->BuildDir, FALSE);
		$File = NULL;

		foreach($Indexer as $File)
		Common\Filesystem\Util::Delete($File);

		return;
	}

	public function
	CompilePharOfBuild():
	void {

		$Files = $this->IndexFilesInBuild();
		$Outfile = Common\Filesystem\Util::Pathify($this->BuildDir, $this->OutputFile);
		$Phar = new Phar($Outfile);

		$Phar->StartBuffering();
		$Phar->SetDefaultStub($this->Bin);
		$Phar->BuildFromIterator($Files, $this->BuildDir);
		$Phar->StopBuffering();

		return;
	}

	public function
	InspectPharOfBuild():
	void {

		$Outfile = Common\Filesystem\Util::Pathify($this->BuildDir, $this->OutputFile);
		$Check = new Phar($Outfile);
		$Item = NULL;

		foreach(new RecursiveIteratorIterator($Check) as $Item)
		echo (preg_replace(
			'#^phar:\/\/(?:.+).phar#', 'pharout://',
			$Item->GetPathName()
		)), PHP_EOL;

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	GetFileCopyMap():
	Common\Datastore {

		$Output = new Common\Datastore;

		($this->Files)
		->Each(function(string $F) use($Output) {
			$Key = Common\Filesystem\Util::Pathify($this->BaseDir, $F);
			$Val = Common\Filesystem\Util::Pathify($this->BuildDir, $F);
			$Output->Set($Key, $Val);
			return;
		});

		return $Output;
	}

	public function
	GetDefaultBaseDir():
	?string {

		// try to determine a relevant basedir from the index of files.

		$Chomped = $this->Files->Map(
			fn(string $P)
			=> explode(DIRECTORY_SEPARATOR, $P)
		);

		if($Chomped->Count() === 1) {
			$Bits = Common\Datastore::FromArray($Chomped[0]);
			$Bits->Pop();
			$Path = $Bits->Join(DIRECTORY_SEPARATOR);

			return realpath($Path) ?: NULL;
		}

		return NULL;
	}

	public function
	GetDefaultBuildDir():
	string {

		$Output = Common\Filesystem\Util::Pathify(
			getcwd(), 'build'
		);

		return $Output;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	From(string $PharOut, string $Bin, Common\Datastore $Files, ?string $BaseDir=NULL, ?string $BuildDir=NULL, ?Common\Datastore $FileFilters=NULL):
	static {

		$Output = new static;

		($Output)
		->SetOutputFile($PharOut)
		->SetBin($Bin)
		->SetFileList($Files)
		->SetBaseDir($BaseDir)
		->SetBuildDir($BuildDir)
		->SetFileFilters($FileFilters);

		return $Output;
	}

}
