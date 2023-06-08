<?php

namespace Nether\Common;

use PHPUnit\Framework\TestCase;

class PasswordTesterTest
extends TestCase {

	/** @test */
	public function
	TestDefaultStricts():
	void {

		$Dataset = [
			0             => FALSE,
			1             => FALSE,
			'2'           => FALSE,
			'three'       => FALSE,
			'Three'       => FALSE,
			'Three3'      => FALSE,
			'ThreeFour5'  => FALSE,
			'threefour5'  => FALSE,
			'!!!!!!!!!!'  => FALSE,
			'ThreeFour!'  => FALSE,
			'Three4our!'  => TRUE,
			'TreeFour!'   => FALSE,
			'Tree4our!'   => FALSE,
			'ThreeFour5!' => TRUE,
			'threefour5!' => FALSE,
			'!!!!!Four5'  => TRUE
		];

		$Tester = new PasswordTester;
		$Input = NULL;
		$Expect = NULL;
		$OK = NULL;

		foreach($Dataset as $Input => $Expect) {
			$OK = $Tester->IsOK($Input);

			if(!$Expect) {
				$this->AssertFalse($OK, $Input);
				continue;
			}

			$this->AssertTrue($OK, $Input);
		}

		return;
	}

	/** @test */
	public function
	TestChangeMinLength():
	void {

		$Dataset = [
			0             => FALSE,
			1             => FALSE,
			'2'           => FALSE,
			'three'       => FALSE,
			'Three'       => FALSE,
			'Three3'      => FALSE,
			'ThreeFour5'  => FALSE,
			'threefour5'  => FALSE,
			'!!!!!!!!!!'  => FALSE,
			'ThreeFour!'  => FALSE,
			'Three4our!'  => TRUE,
			'TreeFour!'   => FALSE,
			'Tree4our!'   => TRUE,
			'ThreeFour5!' => TRUE,
			'threefour5!' => FALSE,
			'!!!!!Four5'  => TRUE
		];

		$Tester = new PasswordTester;
		$Tester->SetMinLength(8);

		$Input = NULL;
		$Expect = NULL;
		$OK = NULL;

		foreach($Dataset as $Input => $Expect) {
			$OK = $Tester->IsOK($Input);

			if(!$Expect) {
				$this->AssertFalse($OK, $Input);
				continue;
			}

			$this->AssertTrue($OK, $Input);
		}

		return;
	}

	/** @test */
	public function
	TestDisableRequireNum():
	void {

		$Dataset = [
			0             => FALSE,
			1             => FALSE,
			'2'           => FALSE,
			'three'       => FALSE,
			'Three'       => FALSE,
			'Three3'      => FALSE,
			'ThreeFour5'  => FALSE,
			'threefour5'  => FALSE,
			'!!!!!!!!!!'  => FALSE,
			'ThreeFour!'  => TRUE,
			'Three4our!'  => TRUE,
			'TreeFour!'   => FALSE,
			'Tree4our!'   => FALSE,
			'ThreeFour5!' => TRUE,
			'threefour5!' => FALSE,
			'!!!!!Four5'  => TRUE
		];

		$Tester = new PasswordTester;
		$Tester->SetRequireNumeric(FALSE);

		$Input = NULL;
		$Expect = NULL;
		$OK = NULL;

		foreach($Dataset as $Input => $Expect) {
			$OK = $Tester->IsOK($Input);

			if(!$Expect) {
				$this->AssertFalse($OK, $Input);
				continue;
			}

			$this->AssertTrue($OK, $Input);
		}

		return;
	}

	/** @test */
	public function
	TestDisableRequireSpecial():
	void {

		$Dataset = [
			0             => FALSE,
			1             => FALSE,
			'2'           => FALSE,
			'three'       => FALSE,
			'Three'       => FALSE,
			'Three3'      => FALSE,
			'ThreeFour5'  => TRUE,
			'threefour5'  => FALSE,
			'!!!!!!!!!!'  => FALSE,
			'ThreeFour!'  => FALSE,
			'Three4our!'  => TRUE,
			'TreeFour!'   => FALSE,
			'Tree4our!'   => FALSE,
			'ThreeFour5!' => TRUE,
			'threefour5!' => FALSE,
			'!!!!!Four5'  => TRUE
		];

		$Tester = new PasswordTester;
		$Tester->SetRequireSpecial(FALSE);

		$Input = NULL;
		$Expect = NULL;
		$OK = NULL;

		foreach($Dataset as $Input => $Expect) {
			$OK = $Tester->IsOK($Input);

			if(!$Expect) {
				$this->AssertFalse($OK, $Input);
				continue;
			}

			$this->AssertTrue($OK, $Input);
		}

		return;
	}

	/** @test */
	public function
	TestDisableRequireCasing():
	void {

		$Dataset = [
			0             => FALSE,
			1             => FALSE,
			'2'           => FALSE,
			'three'       => FALSE,
			'Three'       => FALSE,
			'Three3'      => FALSE,
			'ThreeFour5'  => FALSE,
			'threefour5'  => FALSE,
			'!!!!!!!!!!'  => FALSE,
			'ThreeFour!'  => FALSE,
			'Three4our!'  => TRUE,
			'TreeFour!'   => FALSE,
			'Tree4our!'   => FALSE,
			'ThreeFour5!' => TRUE,
			'threefour5!' => TRUE,
			'!!!!!Four5'  => TRUE
		];

		$Tester = new PasswordTester;
		$Tester->SetRequireAlphaLower(FALSE);
		$Tester->SetRequireAlphaUpper(FALSE);

		$Input = NULL;
		$Expect = NULL;
		$OK = NULL;

		foreach($Dataset as $Input => $Expect) {
			$OK = $Tester->IsOK($Input);

			if(!$Expect) {
				$this->AssertFalse($OK, $Input);
				continue;
			}

			$this->AssertTrue($OK, $Input);
		}

		return;
	}

	/** @test */
	public function
	TestFullDumbass():
	void {

		$Dataset = [
			0             => FALSE,
			1             => FALSE,
			'2'           => FALSE,
			'three'       => TRUE,
			'Three'       => TRUE,
			'Three3'      => TRUE,
			'ThreeFour5'  => TRUE,
			'threefour5'  => TRUE,
			'!!!!!!!!!!'  => TRUE,
			'ThreeFour!'  => TRUE,
			'Three4our!'  => TRUE,
			'TreeFour!'   => TRUE,
			'Tree4our!'   => TRUE,
			'ThreeFour5!' => TRUE,
			'threefour5!' => TRUE,
			'!!!!!Four5'  => TRUE
		];

		$Tester = new PasswordTester;
		$Tester->SetMinLength(4);
		$Tester->SetRequireAlphaLower(FALSE);
		$Tester->SetRequireAlphaUpper(FALSE);
		$Tester->SetRequireNumeric(FALSE);
		$Tester->SetRequireSpecial(FALSE);

		$Input = NULL;
		$Expect = NULL;
		$OK = NULL;

		foreach($Dataset as $Input => $Expect) {
			$OK = $Tester->IsOK($Input);

			if(!$Expect) {
				$this->AssertFalse($OK, $Input);
				continue;
			}

			$this->AssertTrue($OK, $Input);
		}

		return;
	}

	/** @test */
	public function
	TestDescriptionGenerator():
	void {

		$Tester = new PasswordTester;

		$this->AssertEquals(
			'Must contain at least 10 characters with a lowercase letter (a-z), an uppercase letter (A-Z), a number (0-9), and a special character.',
			$Tester->GetDescription()
		);

		$Tester->SetMinLength(69);
		$this->AssertEquals(
			'Must contain at least 69 characters with a lowercase letter (a-z), an uppercase letter (A-Z), a number (0-9), and a special character.',
			$Tester->GetDescription()
		);

		$Tester->SetRequireNumeric(FALSE);
		$this->AssertEquals(
			'Must contain at least 69 characters with a lowercase letter (a-z), an uppercase letter (A-Z), and a special character.',
			$Tester->GetDescription()
		);

		$Tester->SetRequireSpecial(FALSE);
		$this->AssertEquals(
			'Must contain at least 69 characters with a lowercase letter (a-z), and an uppercase letter (A-Z).',
			$Tester->GetDescription()
		);

		$Tester->SetRequireAlphaUpper(FALSE);
		$this->AssertEquals(
			'Must contain at least 69 characters with a lowercase letter (a-z).',
			$Tester->GetDescription()
		);

		$Tester->SetRequireAlphaLower(FALSE);
		$this->AssertEquals(
			'Must contain at least 69 characters.',
			$Tester->GetDescription()
		);


		return;
	}

}