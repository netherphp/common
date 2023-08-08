<?php

namespace Nether\Common;

use PHPUnit\Framework\TestCase;

class PatchableTest1
extends Prototype {

	public int $ID;

	#[Meta\PropertyPatchable]
	#[Meta\PropertyFilter([ Filters\Text::class, 'Trimmed' ])]
	public string $Name;

	#[Meta\PropertyPatchable]
	#[Meta\PropertyFilter([ Filters\Text::class, 'Trimmed' ])]
	#[Meta\PropertyFilter([ Filters\Text::class, 'Stripped' ])]
	public string $Food;

}

class PrototypePropertyPatchableTest
extends TestCase {

	/** @test */
	public function
	TestBasic():
	void {


		$Props = PatchableTest1::GetPropertyIndex();

		$Patchable = PatchableTest1::GetPropertiesWithAttribute(
			Meta\PropertyPatchable::class
		);

		$this->AssertCount(3, $Props);
		$this->AssertCount(2, $Patchable);

		////////

		$Prop = NULL;
		$Filters = NULL;
		$Filter = NULL;
		$Value = NULL;

		foreach($Patchable as $Prop) {
			$Filters = $Prop->GetAttributes(Meta\PropertyFilter::class);

			if($Prop->Name === 'Name') {
				$this->AssertCount(1, $Filters);
				$Value = ' Bob ';

				foreach($Filters as $Filter)
				$Value = $Filter($Value);

				$this->AssertEquals('Bob', $Value);
			}

			if($Prop->Name === 'Food') {
				$this->AssertCount(2, $Filters);
				$Value = ' <b>Bob</b> ';

				foreach($Filters as $Filter)
				$Value = $Filter($Value);

				$this->AssertEquals('Bob', $Value);
			}
		}

		return;
	}

	/** @test */
	public function
	TestBasicHelperMethod():
	void {


		$Patchable = Meta\PropertyPatchable::FromClass(
			PatchableTest1::class
		);

		$this->AssertCount(2, $Patchable);

		////////

		$Prop = NULL;
		$Filters = NULL;
		$Filter = NULL;
		$Value = NULL;

		foreach($Patchable as $Prop => $Filters) {

			if($Prop === 'Name') {
				$this->AssertCount(1, $Filters);
				$Value = ' Bob ';

				foreach($Filters as $Filter)
				$Value = $Filter($Value);

				$this->AssertEquals('Bob', $Value);
			}

			if($Prop === 'Food') {
				$this->AssertCount(2, $Filters);
				$Value = ' <b>Bob</b> ';

				foreach($Filters as $Filter)
				$Value = $Filter($Value);

				$this->AssertEquals('Bob', $Value);
			}
		}

		return;
	}

}