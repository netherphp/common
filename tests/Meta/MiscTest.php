<?php

namespace NetherTestSuite\Common;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Nether\Common;

use PHPUnit\Framework\TestCase;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class TestMiscMetaAttr
extends Common\Prototype {

	#[Common\Meta\Deprecated('2023-08-13')]
	public string
	$Dep;

	#[Common\Meta\PropertyListable]
	public int
	$FourtyTwo = 42;

};

class MiscTest
extends TestCase {

	/** @test */
	public function
	TestListable():
	void {

		$Listable = TestMiscMetaAttr::GetPropertiesWithAttribute(
			Common\Meta\PropertyListable::class
		);

		$this->AssertEquals(1, count($Listable));

		///////

		$Listable = Common\Meta\PropertyListable::FromClass(
			TestMiscMetaAttr::class
		);

		$this->AssertEquals(1, count($Listable));

		return;
	}

	/** @test */
	public function
	TestDeprecated():
	void {

		$Deps = TestMiscMetaAttr::GetPropertiesWithAttribute(
			Common\Meta\Deprecated::class
		);

		$this->AssertEquals(1, count($Deps));

		return;
	}

}