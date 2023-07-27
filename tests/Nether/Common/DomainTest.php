<?php

namespace Nether\Common;

use PHPUnit\Framework\TestCase;

class DomainTest
extends TestCase {

	/** @test */
	public function
	TestDefaults():
	void {

		$Domain = new Struct\Domain('www.pegasusgate.net');

		// check state

		$this->AssertEquals('www.pegasusgate.net', $Domain->GetInput());
		$this->AssertEquals(2, $Domain->GetLevel());
		$this->AssertEquals('.', $Domain->GetDelim());

		// check behaviour

		$this->AssertEquals('pegasusgate.net', $Domain->Get());
		$this->AssertEquals('pegasusgate.net', (string)$Domain);

		return;
	}

	/** @test */
	public function
	TestFactoryDefaults():
	void {

		$Domain = Struct\Domain::FromDomain('www.pegasusgate.net');

		// check state

		$this->AssertEquals('www.pegasusgate.net', $Domain->GetInput());
		$this->AssertEquals(2, $Domain->GetLevel());
		$this->AssertEquals('.', $Domain->GetDelim());

		// check behaviour

		$this->AssertEquals('pegasusgate.net', $Domain->Get());
		$this->AssertEquals('pegasusgate.net', (string)$Domain);

		return;
	}

	/** @test */
	public function
	TestInvokeSyntax():
	void {

		$Domain = new Struct\Domain('www.pegasusgate.net');

		// invoke no args straight get.

		$this->AssertEquals('pegasusgate.net', $Domain());
		$this->AssertEquals('www.pegasusgate.net', $Domain->GetInput());

		// invoke with string arg set then get.

		$this->AssertEquals('yourmom.com', $Domain('www.yourmom.com'));
		$this->AssertEquals('www.yourmom.com', $Domain->GetInput());

		return;
	}

	/** @test */
	public function
	TestDomainLevel():
	void {

		// out of range - high. (technically no such thing)

		$Domain = new Struct\Domain('www.pegasusgate.net', 42);
		$this->AssertEquals('www.pegasusgate.net', $Domain->GetInput());
		$this->AssertEquals('www.pegasusgate.net', $Domain->Get());

		// reasoanble subdomain mode.

		$Domain->SetLevel(3);
		$this->AssertEquals('www.pegasusgate.net', $Domain->GetInput());
		$this->AssertEquals('www.pegasusgate.net', $Domain->Get());

		// primary domain mode.

		$Domain->SetLevel(2);
		$this->AssertEquals('www.pegasusgate.net', $Domain->GetInput());
		$this->AssertEquals('pegasusgate.net', $Domain->Get());

		// tld mode.

		$Domain->SetLevel(1);
		$this->AssertEquals('www.pegasusgate.net', $Domain->GetInput());
		$this->AssertEquals('net', $Domain->Get());

		// out of range - low. (min 1 tld mode)

		$Domain->SetLevel(0);
		$this->AssertEquals('www.pegasusgate.net', $Domain->GetInput());
		$this->AssertEquals('net', $Domain->Get());

		return;
	}

	/** @test */
	public function
	TestDomainDelim():
	void {

		$Domain = new Struct\Domain('www-pegasusgate-net');
		$Domain->SetDelim('-');

		$this->AssertEquals('pegasusgate-net', $Domain->Get());

		return;
	}

}