<?php

namespace Nether\Common;

use PHPUnit\Framework\TestCase;

use DateTime;
use DateTimeZone;

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

		$DateObj->SetDateFormat('Y.m.d');
		$this->AssertEquals(date('Y-m-d', $Time), $DateObj('Y-m-d'));
		$this->AssertEquals(date('Y.m.d', $Time), $DateObj());

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
		$DateObj->SetTimeFormat('H.i.s');

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

	/** @test */
	public function
	TestUnixtime1():
	void {

		$When = 'January 1 1970 00:00:00 +0000';
		$Date = new Date($When);
		$Before = -1;
		$After = -2;

		$Date->SetTimezone('UTC');
		$Before = $Date->GetUnixtime();

		$Date->SetTimezone('+0600');
		$After = $Date->GetUnixtime();

		$this->AssertEquals(0, $Before);
		$this->AssertEquals(0, $After);

		return;
	}

	/** @test */
	public function
	TestUnixtime2():
	void {

		$When = 'January 1 1970 06:00:00 +0600';
		$Date = new Date($When);
		$Before = -1;
		$After = -2;

		$Date->SetTimezone('UTC');
		$Before = $Date->GetUnixtime();

		$Date->SetTimezone('+0600');
		$After = $Date->GetUnixtime();

		$this->AssertEquals(0, $Before);
		$this->AssertEquals(0, $After);

		return;
	}

	/** @test */
	public function
	TestUnixtime3():
	void {

		Library::Set(Date::ConfDefaultTimezone, '+0600');

		$When = 'January 1 1970 00:00:00';
		$Date = new Date($When);

		$DateBefore = '-1';
		$TimeBefore = -1;
		$DateAfter = '-1';
		$TimeAfter = -2;

		$DateBefore = $Date->Get(DateTime::RFC822);
		$TimeBefore = $Date->GetUnixtime();

		$Date->SetTimezone('UTC');
		$DateAfter = $Date->Get(DateTime::RFC822);
		$TimeAfter = $Date->GetUnixtime();

		$this->AssertEquals('Thu, 01 Jan 70 06:00:00 +0600', $DateBefore);
		$this->AssertEquals(0, $TimeBefore);
		$this->AssertEquals('Thu, 01 Jan 70 00:00:00 +0000', $DateAfter);
		$this->AssertEquals(0, $TimeAfter);

		Library::Set(Date::ConfDefaultTimezone, NULL);

		return;
	}
}