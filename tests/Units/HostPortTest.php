<?php

namespace NetherTestSuite\Common;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use PHPUnit;

use Nether\Common\Units\HostPort;
use Nether\Common\Error\FormatInvalid;
use Exception;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class HostPortTest
extends PHPUnit\Framework\TestCase {

	/** @test */
	public function
	TestConstruct():
	void {

		$Host = 'pegasusgate.net';
		$Port = 80;
		$Obj = new HostPort($Host, $Port);

		$this->AssertEquals($Host, $Obj->GetHost());
		$this->AssertEquals($Port, $Obj->GetPort());

		return;
	}

	/** @test */
	public function
	TestFromString():
	void {

		$Host = 'pegasusgate.net';
		$Port = 80;
		$Exceptional = NULL;
		$Err = NULL;

		// test it works as expected.

		$Obj = HostPort::FromString("{$Host}:{$Port}");
		$this->AssertEquals($Host, $Obj->GetHost());
		$this->AssertEquals($Port, $Obj->GetPort());

		// test invalid format.

		try {
			$Exceptional = FALSE;
			$Obj = HostPort::FromString($Host);
		}

		catch(Exception $Err) {
			$Exceptional = TRUE;
			$this->AssertInstanceOf(FormatInvalid::class, $Err);
		}

		$this->AssertTrue($Exceptional);

		return;
	}

	/** @test */
	public function
	TestFromStringDefaultPort():
	void {

		$Host = 'pegasusgate.net';
		$Port = 80;
		$DefaultPort = 1234;

		$Obj = HostPort::FromStringDefaultPort("{$Host}:{$Port}", $DefaultPort);
		$this->AssertEquals($Host, $Obj->GetHost());
		$this->AssertEquals($Port, $Obj->GetPort());

		$Obj = HostPort::FromStringDefaultPort($Host, $DefaultPort);
		$this->AssertEquals($Host, $Obj->GetHost());
		$this->AssertEquals($DefaultPort, $Obj->GetPort());


		return;
	}

}