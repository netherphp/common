<?php

namespace Nether\Common;

use PHPUnit\Framework\TestCase;
use Exception;

class OneScriptTest
extends TestCase {

	/** @test */
	public function
	TestBasic():
	void {

		$Scripts = new OneScript(dirname(__FILE__, 4), 'styles.css');
		$Scripts->AddFile('testdata/css/one.css');
		$Scripts->AddFile('testdata/css/two.css');

		////////

		$Scripts->Render();
		$Filedata1 = file_get_contents('./styles.css');

		$Scripts->Render();
		$Filedata2 = file_get_contents('./styles.css');

		////////

		// the files should be exact including the timestamp as the second
		// render should have bailed on its own having seen no updates.

		$this->AssertEquals(
			$Scripts->GetFileSize('styles.css'),
			strlen($Filedata1)
		);

		$this->AssertEquals(
			$Scripts->GetFileSize('styles.css'),
			strlen($Filedata2)
		);

		unlink('./styles.css');

		$this->AssertEquals($Filedata1, $Scripts->GetOutput());
		$this->AssertEquals($Filedata1, $Filedata2);

		$this->AssertTrue(str_contains($Filedata1, 'one.css'));
		$this->AssertTrue(str_contains($Filedata1, 'two.css'));

		$this->AssertFalse(str_contains($Filedata1, 'one.txt'));
		$this->AssertFalse(str_contains($Filedata1, 'two.txt'));

		return;
	}

	/** @test */
	public function
	TestBasic2():
	void {

		$Scripts = new OneScript(dirname(__FILE__, 4), 'styles.txt');
		$Scripts->AddDir('testdata/css');
		$Scripts->Render();

		$Filedata = file_get_contents('./styles.txt');
		unlink('./styles.txt');

		$this->AssertEquals($Filedata, $Scripts->GetOutput());

		$this->AssertTrue(str_contains($Filedata, 'one.txt'));
		$this->AssertTrue(str_contains($Filedata, 'two.txt'));

		$this->AssertFalse(str_contains($Filedata, 'one.css'));
		$this->AssertFalse(str_contains($Filedata, 'two.css'));

		return;
	}

	/** @test */
	public function
	TestBasicPrint():
	void {

		$Scripts = new OneScript(dirname(__FILE__, 4), 'styles.css');
		$Scripts->AddFile('testdata/css/one.css');
		$Scripts->AddFile('testdata/css/two.css');

		ob_start();
		$Scripts->Print();
		$Outdata = ob_get_clean();

		if(function_exists('xdebug_get_headers')) {
			//$Headers = new Datastore(xdebug_get_headers());
			//$Headers->Remap(fn($Line)=> strtolower(explode(':', $Line)[0]));
			//$this->AssertFalse($Headers->HasValue('content-type'));
		}

		$Filedata = file_get_contents('./styles.css');
		$this->AssertEquals($Filedata, $Outdata);
		unlink('./styles.css');

		return;
	}

	/**
	 * @test
	 * @runInSeparateProcess
	 */
	public function
	TestBasicPrint2():
	void {

		$Scripts = new OneScript(dirname(__FILE__, 4), 'styles.css');
		$Scripts->AddFile('testdata/css/one.css');
		$Scripts->AddFile('testdata/css/two.css');
		$Scripts->Render();

		ob_start();
		$Scripts->Print(TRUE);
		$Outdata = ob_get_clean();

		if(function_exists('xdebug_get_headers')) {
			//$Headers = new Datastore(xdebug_get_headers());
			//$Headers->Remap(fn($Line)=> strtolower(explode(':', $Line)[0]));
			//$this->AssertTrue($Headers->HasValue('content-type'));
		}

		$Filedata = file_get_contents('./styles.css');
		$this->AssertEquals($Filedata, $Outdata);
		unlink('./styles.css');

		return;
	}

	/** @test */
	public function
	TestCrappyFilename():
	void {

		$Exceptional = FALSE;

		try {
			// onescript demands file extensions.
			$OneScript = new OneScript('.', 'derp');
		}

		catch(Exception $Error) {
			$Exceptional = TRUE;
		}

		$this->AssertTrue($Exceptional);

		return;
	}

}