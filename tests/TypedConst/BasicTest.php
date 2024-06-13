<?php

namespace NetherTestSuite\Common\TypedConst;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use PHPUnit\Framework\TestCase;
use Nether\Common\TypedConst;
use Exception;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class CTest1
extends TypedConst {

	const
	One   = 'n1',
	Two   = 'n2',
	Three = 'n3';

	const
	TypeList = [
		self::One   => [ 'One', 'Ones' ],
		self::Two   => [ 'Two', 'Twos' ],
		self::Three => [ 'Three', 'Threes' ]
	];

}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class BasicTest
extends TestCase {

	/** @test */
	public function
	BasicTest():
	void {

		$this->AssertEquals('n1', CTest1::One);

		$this->AssertEquals('One', CTest1::Word(CTest1::One));
		$this->AssertEquals('One', CTest1::Word(CTest1::One, CTest1::WordSingular));
		$this->AssertEquals('Ones', CTest1::Word(CTest1::One, CTest1::WordPlural));
		$this->AssertEquals('Unknown', CTest1::Word('lkdj;fljl;kajf'));

		return;
	}

	/** @test */
	public function
	ListTest():
	void {

		$List = CTest1::List();

		$this->AssertCount(3, $List);
		$this->AssertEquals('One', $List[CTest1::One][TypedConst::WordSingular]);
		$this->AssertEquals('Ones', $List[CTest1::One][TypedConst::WordPlural]);

		return;
	}

	/** @test */
	public function
	KeysTest():
	void {

		$List = CTest1::Keys();

		$this->AssertCount(3, $List);
		$this->AssertEquals(CTest1::One, $List[0]);
		$this->AssertEquals(CTest1::Two, $List[1]);
		$this->AssertEquals(CTest1::Three, $List[2]);

		return;
	}

};

