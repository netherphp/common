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

	/** @test */
	public function
	TestImmutableModify():
	void {

		$Now = new Date('now', FALSE);
		$Then = $Now->Modify('+1 hour');

		$this->AssertTrue($Now === $Then);
		$this->AssertTrue($Now->GetUnixtime() === $Then->GetUnixtime());

		////////

		$Now = new Date('now', TRUE);
		$Then = $Now->Modify('+1 hour');

		$this->AssertFalse($Now === $Then);
		$this->AssertFalse($Now->GetUnixtime() === $Then->GetUnixtime());

		return;
	}

	/** @test */
	public function
	TestCarriedTimezones():
	void {

		$Control = new Date('2001-01-01 01:01am UTC');
		$this->AssertEquals('UTC', $Control->GetTimezoneName());
		$this->AssertEquals(0, $Control->GetTimezoneOffset());

		// should be exactly the same.

		$FromDateTime = new Date($Control->GetDateTime());
		$this->AssertEquals('UTC', $Control->GetTimezoneName());
		$this->AssertEquals(0, $Control->GetTimezoneOffset());

		$FromDateTime = Date::FromDateTime($Control->GetDateTime());
		$this->AssertEquals('UTC', $Control->GetTimezoneName());
		$this->AssertEquals(0, $Control->GetTimezoneOffset());

		// the same but it will lose the timezone name due to how we cloned.

		$FromDate = new Date($Control);
		$this->AssertEquals('+00:00', $FromDate->GetTimezoneName());
		$this->AssertEquals(0, $FromDate->GetTimezoneOffset());

		////////

		$Control->SetTimezone('America/Chicago');
		$this->AssertEquals('America/Chicago', $Control->GetTimezoneName());
		$this->AssertEquals(-21600, $Control->GetTimezoneOffset());

		// should be exactly the same.

		$FromDateTime = new Date($Control->GetDateTime());
		$this->AssertEquals('America/Chicago', $Control->GetTimezoneName());
		$this->AssertEquals(-21600, $Control->GetTimezoneOffset());

		// the same but it will lose the timezone name due to how we cloned.
		$FromDate = new Date($Control);
		$this->AssertEquals('-06:00', $FromDate->GetTimezoneName());
		$this->AssertEquals(-21600, $FromDate->GetTimezoneOffset());

		return;
	}

	/** @test */
	public function
	TestBeforeAfter():
	void {

		$Then = new Date('-1 days');
		$Now = new Date('+1 days');

		////////

		$this->AssertTrue($Then->IsAfter($Now));
		$this->AssertTrue($Now->IsBefore($Then));

		////////

		$this->AssertTrue($Now->IsThisAfter($Then));
		$this->AssertFalse($Now->IsThisBefore($Then));

		$this->AssertFalse($Now->IsThatAfter($Then));
		$this->AssertTrue($Now->IsThatBefore($Then));

		$this->AssertTrue($Then->IsThisBefore($Now));
		$this->AssertFalse($Then->IsThisAfter($Now));

		$this->AssertFalse($Then->IsThatBefore($Now));
		$this->AssertTrue($Then->IsThatAfter($Now));

		return;
	}

	/** @test */
	public function
	TestFetchTimezoneFromSystem():
	void {

		$Magic = Date::FetchTimezoneFromSystem();
		$Other = NULL;

		////////

		if(PHP_OS_FAMILY === 'Windows')
		$Other = Date::FetchTimezoneFromWindows();
		else
		$Other = Date::FetchTimezoneFromUnix();

		////////

		$this->AssertEquals($Other, $Magic);

		return;
	}

	/** @test */
	public function
	TestUnixtime():
	void {

		$Then = Date::FromDateString('-1 day');
		$Time = Date::Unixtime();

		$this->AssertTrue(is_int($Time));
		$this->AssertTrue($Time > $Then->GetUnixtime());

		return;
	}

}