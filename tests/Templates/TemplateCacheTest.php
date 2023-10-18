<?php

namespace NetherTestSuite\Common\TemplateCache;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Nether\Common;
use PHPUnit;

use Exception;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class TemplateCacheTest
extends PHPUnit\Framework\TestCase {

	/** @test */
	public function
	TestBasic():
	void {

		$File = Common\Filesystem\Util::Pathify(dirname(__FILE__), 'TemplateFileTest1.txt');
		$Original = file_get_contents($File);
		$Template = NULL;

		// load file skip cache and verify cache was skipped.

		$Template = new Common\TemplateFile($File);
		$this->AssertEquals(0, Common\TemplateCache::Count());
		$this->AssertFalse(Common\TemplateCache::Has($File));

		// load file hit cache and verify cache was primed.

		$Template = new Common\TemplateFile($File, Cache: TRUE);
		$this->AssertTrue($Template->GetUseCache());
		$this->AssertEquals(1, Common\TemplateCache::Count());
		$this->AssertTrue(Common\TemplateCache::Has($File));
		$this->AssertEquals($Original, $Template->GetData());
		$this->AssertEquals($Original, Common\TemplateCache::Get($File));

		// drop the cache.

		Common\TemplateCache::Drop($File);
		$this->AssertEquals(0, Common\TemplateCache::Count());
		$this->AssertFalse(Common\TemplateCache::Has($File));

		// drop the cache harder.

		Common\TemplateCache::Set($File, $Original);
		$this->AssertEquals(1, Common\TemplateCache::Count());
		$this->AssertTrue(Common\TemplateCache::Has($File));

		Common\TemplateCache::Flush();
		$this->AssertEquals(0, Common\TemplateCache::Count());
		$this->AssertFalse(Common\TemplateCache::Has($File));

		return;
	}


}
