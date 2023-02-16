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

		$this->AssertTrue(str_contains($Filedata, 'testdata/css/one.css'));
		$this->AssertTrue(str_contains($Filedata, 'testdata/css/two.css'));

		return;
	}


}