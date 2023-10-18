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
	TestLoadingNotFound():
	void {

		$Template = NULL;
		$File = 'qwertyuiopasdfghjklzxcvbnm';
		$Exceptional = FALSE;
		$Err = NULL;

		try { $Template = Common\TemplateFile::FromFile($File); }
		catch(Exception $Err) { $Exceptional = TRUE; }

		$this->AssertNull($Template);
		$this->AssertTrue($Exceptional);
		$this->AssertInstanceOf(Common\Error\FileNotFound::class, $Err);

		return;
	}

	/** @test */
	public function
	TestLoadingUnreadable():
	void {

		if(PHP_OS_FAMILY === 'Windows') {
			$this->MarkTestSkipped('Fileperms on Windows is meh.');
			return;
		}

		////////

		$File = Common\Filesystem\Util::Pathify(dirname(__FILE__), 'TemplateFileTest1.txt');
		$Template = NULL;
		$Exceptional = FALSE;
		$Err = NULL;

		try {
			chmod($File, 0o000);
			$Template = Common\TemplateFile::FromFile($File);
		}

		catch(Exception $Err) { $Exceptional = TRUE; }

		chmod($File, 0o666);
		$this->AssertNull($Template);
		$this->AssertTrue($Exceptional);
		$this->AssertInstanceOf(Common\Error\FileUnreadable::class, $Err);

		return;
	}

	/** @test */
	public function
	TestLoadingNormal():
	void {

		// testing file normal.

		$File = Common\Filesystem\Util::Pathify(dirname(__FILE__), 'TemplateFileTest1.txt');
		$Exceptional = FALSE;
		$Err = NULL;

		try { $Template = Common\TemplateFile::FromFile($File); }
		catch(Exception $Err) { $Exceptional = TRUE; }

		$this->AssertEquals($File, $Template->GetFilename());
		$this->AssertFalse($Exceptional);
		$this->AssertNull($Err);

		// test overwriting the data.

		$Template->SetData(NULL);
		$this->AssertNull($Template->GetData());

		// testing no autoload

		$Exceptional = FALSE;
		$Err = NULL;

		try { $Template = new Common\TemplateFile($File, FALSE); }
		catch(Exception $Err) { $Exceptional = TRUE; }

		$this->AssertFalse($Exceptional);
		$this->AssertTrue($Err === NULL);
		$this->AssertNull($Template->GetData());

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
