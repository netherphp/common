<?php

namespace NetherTestSuite;
use PHPUnit;

use Attribute;
use ReflectionMethod;
use ReflectionAttribute;

use Nether\Common\Prototype;
use Nether\Common\Prototype\MethodInfo;
use Nether\Common\Prototype\MethodInfoCache;
use Nether\Common\Prototype\MethodInfoInterface;
use Nether\Common\Package\MethodInfoPackage;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

#[Attribute(Attribute::TARGET_METHOD)]
class TestMethodAttrib1
implements MethodInfoInterface {

	public bool
	$DidMethodInfo = FALSE;

	public function
	OnMethodInfo(MethodInfo $MI, ReflectionMethod $RM, ReflectionAttribute $RA):
	void {

		$this->DidMethodInfo = TRUE;
		return;
	}

}

#[Attribute(Attribute::TARGET_METHOD)]
class TestMethodAttribThereCanBeOnlyOne { }

#[Attribute(Attribute::TARGET_METHOD|Attribute::IS_REPEATABLE)]
class TestMethodAttribHousePartyProtocol { }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class TestClassMethod1 {

	use
	MethodInfoPackage;

	public function
	MethodNoAttrib():
	void {

		return;
	}

	#[TestMethodAttrib1]
	public function
	MethodWithAttrib():
	void {

		return;
	}

	public function
	MethodWithArgs(int $One, int $Two, string $Three):
	void {

		return;
	}

}

class TestClassMethod2
extends Prototype {

	#[TestMethodAttribThereCanBeOnlyOne]
	#[TestMethodAttribHousePartyProtocol]
	#[TestMethodAttribHousePartyProtocol]
	#[TestMethodAttribHousePartyProtocol]
	public function
	Method():
	void {

		return;
	}

	#[TestMethodAttribThereCanBeOnlyOne]
	#[TestMethodAttribHousePartyProtocol]
	#[TestMethodAttribHousePartyProtocol]
	#[TestMethodAttribHousePartyProtocol]
	public function
	OtherMethod():
	void {

		return;
	}

}

class TestClassMethod4
extends Prototype {

	public function
	PublicMethod():
	void {

		return;
	}

	protected function
	ProtectedMethod():
	void {

		return;
	}

	private function
	PrivateMethod():
	void {

		return;
	}

}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class MethodInfoPackageTest
extends PHPUnit\Framework\TestCase {

	/** @test */
	public function
	TestMethodInfoFetchGet():
	void {

		// check that raw reading works as expected.

		$Info1 = TestClassMethod1::FetchMethodInfo('MethodWithAttrib');
		$Info2 = TestClassMethod1::FetchMethodInfo('MethodWithAttrib');
		$Null = TestClassMethod1::FetchMethodInfo('DoesNotExist');

		$this->AssertInstanceOf(MethodInfo::class, $Info1);
		$this->AssertTrue($Info1 !== $Info2);
		$this->AssertNull($Null);

		// check that cached reading worked as expected.

		$Info1 = TestClassMethod1::GetMethodInfo('MethodWithAttrib');
		$Info2 = TestClassMethod1::GetMethodInfo('MethodWithAttrib');
		$Null = TestClassMethod1::GetMethodInfo('DoesNotExist');

		$this->AssertInstanceOf(MethodInfo::class, $Info1);
		$this->AssertTrue($Info1 === $Info2);
		$this->AssertNull($Null);

		return;
	}

	/** @test */
	public function
	TestMethodInfoCacheBasics() {

		$this->AssertFalse(MethodInfoCache::Has('SomethingNeverCached'));
		$this->AssertNull(MethodInfoCache::Get('SomethingNeverCached'));

		return;
	}

	/** @test */
	public function
	TestMethodIndexFetch() {

		// test fetching the index.

		$Methods = TestClassMethod1::FetchMethodIndex();
		$this->AssertTrue(isset($Methods['MethodNoAttrib']));
		$this->AssertTrue(isset($Methods['MethodWithAttrib']));
		$this->AssertTrue(isset($Methods['FetchMethodIndex']));

		// test the index contains things we expected.

		$Info = NULL;

		foreach($Methods as $Info)
		$this->AssertTrue($Info instanceof MethodInfo);

		return;
	}

	/** @test */
	public function
	TestMethodIndexCache() {

		// test that the cache appeared to be working.

		$Methods = TestClassMethod1::GetMethodIndex();
		$this->AssertTrue(isset($Methods['MethodNoAttrib']));
		$this->AssertTrue(isset($Methods['MethodWithAttrib']));
		$this->AssertTrue(isset($Methods['FetchMethodIndex']));
		$this->AssertTrue(MethodInfoCache::Has(TestClassMethod1::class));
		$this->AssertEquals(
			count(MethodInfoCache::Get(TestClassMethod1::class)),
			count($Methods)
		);

		// test that the cache actually works by confirming the info
		// instances are the same copies.

		$Cached = TestClassMethod1::GetMethodIndex();
		$Key = NULL;
		$Info = NULL;

		foreach($Cached as $Key => $Info) {
			$this->AssertTrue($Info instanceof MethodInfo);
			$this->AssertTrue($Methods[$Key] === $Info);
		}

		// check we can flush the cache.

		$this->AssertTrue(MethodInfoCache::Has(TestClassMethod1::class));
		MethodInfoCache::Drop(TestClassMethod1::class);
		$this->AssertFalse(MethodInfoCache::Has(TestClassMethod1::class));

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	/** @test */
	public function
	TestMethodIndexFetchFilteredByAttr() {

		// test fetching the index.

		$Methods = TestClassMethod1::FetchMethodsWithAttribute(TestMethodAttrib1::class);
		$this->AssertFalse(isset($Methods['MethodNoAttrib']));
		$this->AssertTrue(isset($Methods['MethodWithAttrib']));
		$this->AssertEquals(count($Methods), 1);

		// test the index contains things we expected.

		$Info = NULL;

		foreach($Methods as $Info)
		$this->AssertTrue($Info instanceof MethodInfo);

		return;
	}

	/** @test */
	public function
	TestMethodIndexCacheFilteredByAttr() {

		// test fetching the index.

		$Methods = TestClassMethod1::GetMethodsWithAttribute(TestMethodAttrib1::class);
		$Methods = TestClassMethod1::GetMethodsWithAttribute(TestMethodAttrib1::class);
		$this->AssertFalse(isset($Methods['MethodNoAttrib']));
		$this->AssertTrue(isset($Methods['MethodWithAttrib']));
		$this->AssertEquals(count($Methods), 1);

		// test the index contains things we expected.

		$Info = NULL;

		foreach($Methods as $Info)
		$this->AssertTrue($Info instanceof MethodInfo);

		// check fetching multiuse attribs.

		$Methods = TestClassMethod2::GetMethodsWithAttribute(TestMethodAttribHousePartyProtocol::class);

		$this->AssertCount(2, $Methods);

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	/** @test */
	public function
	TestMethodIndexFetchMethodAttribs():
	void {

		$Methods = TestClassMethod1::FetchMethodIndex();
		$AttribYep = $Methods['MethodWithAttrib'];
		$AttribNope = $Methods['MethodNoAttrib'];

		$this->AssertEquals(count($AttribYep->Attributes), 1);
		$this->AssertEquals(count($AttribNope->Attributes), 0);

		$this->AssertArrayHasKey(
			TestMethodAttrib1::class,
			$AttribYep->Attributes
		);

		$this->AssertInstanceOf(
			TestMethodAttrib1::class,
			$AttribYep->Attributes[TestMethodAttrib1::class]
		);

		return;
	}

	/** @test */
	public function
	TestMethodInfoInterface():
	void {

		$Methods = TestClassMethod1::FetchMethodIndex();
		$Method = $Methods['MethodWithAttrib'];
		$Attrib = $Method->GetAttribute(TestMethodAttrib1::class);

		// test that the attribute implemeneted the method info interface
		// and that the attribute executed the self learning.

		$this->AssertTrue($Attrib instanceof TestMethodAttrib1);
		$this->AssertTrue($Attrib instanceof MethodInfoInterface);
		$this->AssertTrue($Attrib->DidMethodInfo);

		return;
	}

	/** @test */
	public function
	TestPropertyInfoAttributeManageMulti():
	void {

		$Methods = TestClassMethod2::GetMethodIndex();
		$Method = $Methods['Method'];
		$A1 = TestMethodAttribThereCanBeOnlyOne::class;
		$A3 = TestMethodAttribHousePartyProtocol::class;
		$Attrib = NULL;

		// check them raw.

		$this->AssertCount(2, $Method->Attributes);
		$this->AssertInstanceOf($A1, $Method->Attributes[$A1]);
		$this->AssertIsArray($Method->Attributes[$A3]);
		$this->AssertCount(3, $Method->Attributes[$A3]);

		foreach($Method->Attributes[$A3] as $Attrib)
		$this->AssertInstanceOf($A3, $Attrib);

		// check them from the api.

		$this->AssertTrue($Method->HasAttribute($A1));
		$this->AssertFalse($Method->HasAttribute('ThisDoesNotExist'));
		$this->AssertNull($Method->GetAttribute('ThisDoesNotExist'));
		$this->AssertInstanceOf($A1, $Method->GetAttribute($A1));
		$this->AssertIsArray($Method->GetAttribute($A3));
		$this->AssertCount(3, $Method->GetAttribute($A3));

		$this->AssertIsArray($Method->GetAttributes($A1));
		$this->AssertCount(1, $Method->GetAttributes($A1));
		$this->AssertIsArray($Method->GetAttributes($A3));
		$this->AssertCount(3, $Method->GetAttributes($A3));
		$this->AssertIsArray($Method->GetAttributes('ThisDoesNotExist'));
		$this->AssertCount(0, $Method->GetAttributes('ThisDoesNotExist'));

		$this->AssertIsArray($Method->GetAttributes());
		$this->AssertCount(2, $Method->GetAttributes());

		foreach($Method->GetAttribute($A3) as $Attrib)
		$this->AssertInstanceOf($A3, $Attrib);

		return;
	}

	/** @test */
	public function
	TestMethodInfoAccess():
	void {

		$Methods = TestClassMethod4::GetMethodIndex();
		$Method = NULL;

		foreach($Methods as $Method) {
			if($Method->Name === 'PublicMethod')
			$this->AssertEquals('public', $Method->Access);

			if($Method->Name === 'ProtectedMethod')
			$this->AssertEquals('protected', $Method->Access);

			if($Method->Name === 'PrivateMethod')
			$this->AssertEquals('private', $Method->Access);
		}

		return;
	}

	/** @test */
	public function
	TestMethodArgThings():
	void {

		$Info = TestClassMethod1::GetMethodInfo('MethodWithArgs');

		$this->AssertEquals(2, $Info->CountArgsOfType('int'));
		$this->AssertEquals(1, $Info->CountArgsOfType('string'));

		$Ints = $Info->GetArgsOfType('int');
		$this->AssertCount(2, $Ints);
		$this->AssertEquals('One', $Ints[0]);
		$this->AssertEquals('Two', $Ints[1]);

		$Strs = $Info->GetArgsOfType('string');
		$this->AssertCount(1, $Strs);
		$this->AssertEquals('Three', $Strs[0]);

		return;
	}

}
