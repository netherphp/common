<?php

namespace Nether\Common;

use PHPUnit\Framework\TestCase;
use DateTime;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

new Library;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class DateTest
extends TestCase {

	/** @test */
	public function
	TestFromInt():
	void {

		// 2022-02-02 22:22:22 UTC
		// 1643840542

		$Time = 1643840542;
		$When = '2022-02-02 22:22:22 UTC';
		$DateObj = Date::FromTime($Time);
		$Format = NULL;

		$Dataset = [
			Values::DateFormatYMD,
			Values::DateFormatFancyDate,
			Values::DateFormatT12,
			Values::DateFormatYMDT12VZ,
			Values::DateFormatUnix
		];

		$this->AssertEquals($Time, $DateObj->GetUnixtime());

		// test set format and stringable.

		$DateObj->SetDateFormat(Values::DateFormatYMD);
		$this->AssertEquals(gmdate(Values::DateFormatYMD, $Time), (string)$DateObj);

		$DateObj->SetDateFormat(Values::DateFormatFancyDateVerbose);
		$this->AssertEquals(gmdate(Values::DateFormatFancyDateVerbose, $Time), (string)$DateObj);

		// test get method

		foreach($Dataset as $Format)
		$this->AssertEquals(date($Format, $Time), $DateObj->Get($Format));

		// test invokable

		foreach($Dataset as $Format)
		$this->AssertEquals(date($Format, $Time), $DateObj($Format));

		return;
	}

	/** @test */
	public function
	TestFromDateString():
	void {

		$Time = 1643840542;
		$When = '2022-02-02 22:22:22 UTC';
		$DateObj = Date::FromDateString($When);
		$Format = NULL;

		$Dataset = [
			Values::DateFormatYMD,
			Values::DateFormatFancyDate,
			Values::DateFormatT12,
			Values::DateFormatYMDT12VZ,
			Values::DateFormatUnix
		];

		$this->AssertEquals($Time, $DateObj->GetUnixtime());

		// test set format and stringable.

		$DateObj->SetDateFormat(Values::DateFormatYMD);
		$this->AssertEquals(gmdate(Values::DateFormatYMD, $Time), (string)$DateObj);

		$DateObj->SetDateFormat(Values::DateFormatFancyDateVerbose);
		$this->AssertEquals(gmdate(Values::DateFormatFancyDateVerbose, $Time), (string)$DateObj);

		// test get method

		foreach($Dataset as $Format)
		$this->AssertEquals(date($Format, $Time), $DateObj->Get($Format));

		// test invokable

		foreach($Dataset as $Format)
		$this->AssertEquals(date($Format, $Time), $DateObj($Format));

		return;
	}

	/** @test */
	public function
	TestJsonise():
	void {

		$Time = 1643840542;
		$When = '2022-02-02 22:22:22 UTC';

		$DateObj = Date::FromDateString($When);
		$DateObj->SetDateFormat('Y.m.d');
		$DateObj->SetDateFormat('H.i.s');

		$Data = json_decode(json_encode($DateObj), TRUE);

		$this->AssertArrayHasKey('DateTime', $Data);
		$this->AssertArrayHasKey('Unix', $Data);
		$this->AssertArrayHasKey('Date', $Data);
		$this->AssertArrayHasKey('Time', $Data);

		$this->AssertEquals($Data['DateTime'], $DateObj->Get(DateTime::RFC3339));
		$this->AssertEquals($Data['Unix'], $DateObj->GetUnixtime());
		$this->AssertEquals($Data['Date'], $DateObj->Get( $DateObj->GetDateFormat() ));
		$this->AssertEquals($Data['Time'], $DateObj->Get( $DateObj->GetTimeFormat() ));

		return;
	}

	/** @test */
	public function
	TestTimezoneChange():
	void {

		$Time = 1643840542;
		$When = '2022-02-02 22:22:22 UTC';
		$WhenLocal = '2022-02-02 16:22:22 GMT-0600';
		$DateObj = Date::FromDateString($When);
		$Format = Values::DateFormatYMDT24VZ;

		$this->AssertEquals($When, $DateObj->Get($Format));

		$DateObj->SetTimezone(-6);
		$this->AssertEquals($WhenLocal, $DateObj->Get($Format));

		return;
	}

}