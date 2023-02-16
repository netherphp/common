<?php

namespace Nether\Common;

use PHPUnit\Framework\TestCase;

class OneScriptTest
extends TestCase {

	/** @test */
	public function
	TestBasic():
	void {

		$Scripts = new OneScript(dirname(__FILE__, 4), 'styles.css');
		$Scripts->AddFile('testdata/css/one.css');
		$Scripts->AddFile('testdata/css/two.css');
		$Scripts->Render();

		$Filedata = file_get_contents('./styles.css');
		unlink('./styles.css');

		$this->AssertEquals($Filedata, $Scripts->GetOutput());

		$this->AssertTrue(str_contains($Filedata, 'testdata/css/one.css'));
		$this->AssertTrue(str_contains($Filedata, 'testdata/css/two.css'));

		$this->AssertFalse(str_contains($Filedata, 'testdata/css/one.txt'));
		$this->AssertFalse(str_contains($Filedata, 'testdata/css/two.txt'));

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

		$this->AssertTrue(str_contains($Filedata, 'testdata/css/one.txt'));
		$this->AssertTrue(str_contains($Filedata, 'testdata/css/two.txt'));

		$this->AssertFalse(str_contains($Filedata, 'testdata/css/one.css'));
		$this->AssertFalse(str_contains($Filedata, 'testdata/css/two.css'));

		return;
	}

	/** @test */
	public function
	TestBasicPrint():
	void {

		$Scripts = new OneScript(dirname(__FILE__, 4), 'styles.css');
		$Scripts->AddFile('testdata/css/one.css');
		$Scripts->AddFile('testdata/css/two.css');
		$Scripts->Render();

		ob_start();
		$Scripts->Print();
		$Outdata = ob_get_clean();

		$Headers = new Datastore(headers_list());
		$Headers->Remap(fn($Line)=> strtolower(explode(':', $Line)[0]));
		$this->AssertFalse($Headers->HasValue('content-type'));

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
			$Headers = new Datastore(xdebug_get_headers());
			$Headers->Remap(fn($Line)=> strtolower(explode(':', $Line)[0]));
			$this->AssertTrue($Headers->HasValue('content-type'));
		}

		$Filedata = file_get_contents('./styles.css');
		$this->AssertEquals($Filedata, $Outdata);
		unlink('./styles.css');

		return;
	}

}