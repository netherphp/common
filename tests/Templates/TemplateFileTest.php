<?php

namespace NetherTestSuite\Common\TemplateFile;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Nether\Common;
use PHPUnit;

use Exception;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class TemplateFileTest
extends PHPUnit\Framework\TestCase {

	/** @test */
	public function
	TestLoading():
	void {

		$File = 'qwertyuiopasdfghjklzxcvbnm';
		$Exceptional = FALSE;
		$Err = NULL;

		try { $Template = Common\TemplateFile::FromFile($File); }
		catch(Exception $Err) { $Exceptional = TRUE; }

		$this->AssertTrue($Exceptional);
		$this->AssertTrue($Err instanceof Common\Error\FileNotFound);

		////////

		$File = Common\Filesystem\Util::Pathify(dirname(__FILE__), 'TemplateFileTest1.txt');
		$Exceptional = FALSE;
		$Err = NULL;

		try { $Template = Common\TemplateFile::FromFile($File); }
		catch(Exception $Err) { $Exceptional = TRUE; }

		$this->AssertFalse($Exceptional);
		$this->AssertTrue($Err === NULL);

		return;
	}

	/** @test */
	public function
	TestFindingTokens():
	void {

		$Filename = 'TemplateFileTest1.txt';
		$Filepath = Common\Filesystem\Util::Pathify(dirname(__FILE__), $Filename);
		$Template = Common\TemplateFile::FromFile($Filepath);
		$Tokens = $Template->FindTokens();

		$this->AssertInstanceOf(Common\Datastore::class, $Tokens);
		$this->AssertEquals(1, $Tokens->Count());
		$this->AssertTrue($Tokens->HasValue('Filepath'));
		$this->AssertFalse($Tokens->HasValue('ASDFASDFASDFASDF'));

		return;
	}

	/** @test */
	public function
	TestReplacing():
	void {

		$Filename = 'TemplateFileTest1.txt';
		$Filepath = Common\Filesystem\Util::Pathify(dirname(__FILE__), $Filename);
		$Original = file_get_contents($Filepath);
		$Template = Common\TemplateFile::FromFile($Filepath);
		$OutputOK = NULL;
		$OutputKO = NULL;

		$Tokens = [
			'Filepath' => $Filepath
		];

		// see that it read the template source.

		$this->AssertEquals($Original, $Template->GetData());
		$this->AssertFalse(str_contains($Original, $Filepath));

		// see that it did a replace without an update.

		$OutputOK = $Template->ReplaceTokensWith($Tokens);
		$this->AssertTrue(str_contains($OutputOK, $Filepath));
		$this->AssertEquals($Original, $Template->GetData());
		$this->AssertFalse(str_contains($Original, $Filepath));

		// see that it did a replace and an update.

		$OutputKO = $Template->UpdateTokensWith($Tokens)->GetData();
		$this->AssertEquals($OutputOK, $OutputKO);
		$this->AssertTrue(str_contains($OutputKO, $Filepath));
		$this->AssertTrue(str_contains($OutputKO, $Filepath));

		return;
	}

}