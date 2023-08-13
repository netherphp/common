<?php

namespace NetherTestSuite\Common;

use Nether\Common;

use PHPUnit\Framework\TestCase;
use Exception;

class IndexerTest
extends TestCase {

	/** @test */
	public function
	TestBasic():
	void {

		$Path = Common\Filesystem\Util::Pathify(dirname(__FILE__, 3), 'src');
		$File = NULL;

		////////

		$Indexer = new Common\Filesystem\Indexer($Path);
		$Files = new Common\Datastore;

		foreach($Indexer as $File)
		$Files->Push($File->GetPathname());

		$this->AssertEquals(1, $Files->Count());
		$this->AssertTrue(str_ends_with($Files[0], 'Nether'));

		////////

		$Indexer = new Common\Filesystem\Indexer($Path, TRUE);
		$Files = new Common\Datastore;

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