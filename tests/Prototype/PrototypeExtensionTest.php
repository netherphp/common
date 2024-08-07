<?php

namespace NetherTestSuite\Common\Prototype;

use PHPUnit\Framework\TestCase;
use Nether\Common\Prototype;
use Nether\Common\Meta\PropertyOrigin;
use Nether\Common\Meta\PropertyPatchable;
use Nether\Common\Prototype\Flags;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class PrototypeRegionTest
extends Prototype {
/*//
this is a test class to demonstrate the ability of the static property map
to do its job. it is designed to emulate the mutation of an ugly data set
like from the database into properties you actually want to type.
//*/

	#[PropertyOrigin('country_id')]
	public int $ID = 0;

	#[PropertyOrigin('country_code')]
	public ?string $Code = NULL;

	#[PropertyOrigin('country_name')]
	public ?string $Name = NULL;

}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class PrototypeExtensionTest
extends TestCase {

	protected array
	$Input = [
	/*//
	@type Array
	pretend this came from your database. it contains flat underscore scheme
	that is common with db tables, and includes an id which is currently a
	string which is also common with database database results.
	//*/

		'country_id'   => '1',
		'country_code' => 'US',
		'country_name' => 'United States',
		'country_king' => 'Bernie Sanders'
	];

	/** @test */
	public function
	TestMapping() {
	/*//
	checks that the property map was able to rename the properties when they
	were created on this new object.
	//*/

		$Object = new PrototypeRegionTest($this->Input);
		$Old = NULL;
		$Attrib = NULL;

		foreach(PrototypeRegionTest::GetPropertyIndex() as $Old => $Attrib) {
			$this->AssertFalse(property_exists($Object, $Old));
			$this->AssertTrue(property_exists($Object, $Attrib->Name));
		}

		return;
	}

	/** @test */
	public function
	TestMappingDropUnPrototype() {
	/*//
	check that properties which were not Prototype were dropped which is also
	the default behaviour of this object.
	//*/

		$Object = new PrototypeRegionTest(
			$this->Input,
			NULL,
			Flags::StrictInput
		);

		$this->AssertFalse(property_exists($Object, 'country_king'));

		return;
	}

	/** @test */
	public function
	TestMappingIncludeUnPrototype() {
	/*//
	check that we were able to include unPrototype properties as an option.
	//*/

		$Object = new PrototypeRegionTest(
			$this->Input,
			NULL,
			0
		);

		$this->AssertTrue(property_exists($Object, 'country_king'));
		$this->AssertEquals($Object->country_king, $this->Input['country_king']);

		return;
	}

	/** @test */
	public function
	TestMappingWithTypecasting() {
	/*//
	test that the typecasting is being applied to the property map.
	//*/

		$Object = new PrototypeRegionTest($this->Input);
		$this->AssertTrue($Object->ID === (Int)$this->Input['country_id']);

		return;
	}

}
