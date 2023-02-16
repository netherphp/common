<?php

namespace Nether\Common;

use PHPUnit\Framework\TestCase;
use Exception;

class FilesystemIndexerTest
extends TestCase {

	/** @test */
	public function
	TestBasic():
	void {

		$Path = Filesystem\Util::Pathify(dirname(__FILE__, 4), 'src');
		$File = NULL;

		////////

		$Indexer = new Filesystem\Indexer($Path);
		$Files = new Datastore;

		foreach($Indexer as $File)
		$Files->Push($File->GetPathname());

		$this->AssertEquals(1, $Files->Count());
		$this->AssertTrue(str_ends_with($Files[0], 'Nether'));

		////////

		$Indexer = new Filesystem\Indexer($Path, TRUE);
		$Files = new Datastore;

		foreach($Indexer as $File)
		$Files->Push($File->GetPathname());

		$this->AssertGreaterThan(1, $Files->Count());
		$this->AssertEquals(1, (
			$Files
			->Distill(fn($File)=> str_contains($File, 'PasswordTester.php'))
			->Count()
		));

		return;
	}

}