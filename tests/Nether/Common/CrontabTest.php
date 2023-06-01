<?php

namespace Nether\Common;

use PHPUnit;
use Nether\Common;

use Exception;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

new Common\Library;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class CrontabTest
extends PHPUnit\Framework\TestCase {

	/** @test */
	public function
	TestBasic():
	void {

		$Row = new Common\Struct\CrontabEntry;
		$Exceptional = FALSE;

		////////

		// test it defaults as expected. a fresh instance has no command
		// and will refuse to render until given one.

		try {
			$this->AssertNull($Row->Minute);
			$this->AssertNull($Row->Hour);
			$this->AssertNull($Row->Day);
			$this->AssertNull($Row->Month);
			$this->AssertNull($Row->Weekday);
			$this->AssertNull($Row->Command);
			$this->AssertEquals('', $Row->GetAsCrontab());
		}

		catch(Exception $Err) {
			$Exceptional = TRUE;
			$this->AssertInstanceOf(Exception::class, $Err);
		}

		$this->AssertTrue($Exceptional);

		////////

		$Row->Command = 'command';
		$this->AssertEquals('* * * * * command', $Row->GetAsCrontab());

		////////

		$Row->Minute = 1;
		$Row->Hour = 2;
		$Row->Day = 3;
		$Row->Month = 4;
		$Row->Weekday = 5;
		$this->AssertEquals('1 2 3 4 5 command', $Row->GetAsCrontab());

		return;
	}

	/** @test */
	public function
	TestSetupDaily():
	void {

		$Row = Common\Struct\CrontabEntry::FromValues(Command: 'test');
		$this->AssertEquals('* * * * * test', $Row->GetAsCrontab());

		// military time

		$Row->SetupDailyTime(1);
		$this->AssertEquals('1 0 * * * test', $Row->GetAsCrontab());

		$Row->SetupDailyTime(420);
		$this->AssertEquals('20 4 * * * test', $Row->GetAsCrontab());

		$Row->SetupDailyTime('421');
		$this->AssertEquals('21 4 * * * test', $Row->GetAsCrontab());

		$Row->SetupDailyTime(1620);
		$this->AssertEquals('20 16 * * * test', $Row->GetAsCrontab());

		$Row->SetupDailyTime('1621');
		$this->AssertEquals('21 16 * * * test', $Row->GetAsCrontab());

		// pleb time

		$Row->SetupDailyTime('4:20');
		$this->AssertEquals('20 4 * * * test', $Row->GetAsCrontab());

		$Row->SetupDailyTime('4:21am');
		$this->AssertEquals('21 4 * * * test', $Row->GetAsCrontab());

		$Row->SetupDailyTime('4:22pm');
		$this->AssertEquals('22 16 * * * test', $Row->GetAsCrontab());

		$Row->SetupDailyTime('16:23');
		$this->AssertEquals('23 16 * * * test', $Row->GetAsCrontab());

		// daily flag

		$Row->SetupDailyTime(420);
		$this->AssertEquals('20 4 * * * test', $Row->GetAsCrontab());
		$this->AssertTrue($Row->IsDaily());

		$Row->SetupDailyTime(420);
		$Row->SetDayOfMonth(1);
		$this->AssertEquals('20 4 1 * * test', $Row->GetAsCrontab());
		$this->AssertFalse($Row->IsDaily());

		$Row->SetupDailyTime(420);
		$Row->SetMonth(1);
		$this->AssertEquals('20 4 * 1 * test', $Row->GetAsCrontab());
		$this->AssertFalse($Row->IsDaily());

		$Row->SetupDailyTime(420);
		$Row->SetDayOfWeek(1);
		$this->AssertEquals('20 4 * * 1 test', $Row->GetAsCrontab());
		$this->AssertFalse($Row->IsDaily());

		return;
	}

	/** @test */
	public function
	TestValidCrontabStrings():
	void {

		$Tests = [
			// good as is.
			'1 2 3 4 5 command'
			=> '1 2 3 4 5 command',

			// extra white space that should parse without error.
			'1 2 3 4 5  command'
			=> '1 2 3 4 5 command',

			// lots of extra white space that should parse without error.
			'1  2  3  4  5  command'
			=> '1 2 3 4 5 command',

			// wtf are you even doing in there?
			'1	2	3	4	5	command'
			=> '1 2 3 4 5 command',

			// not a valid crontab entry.
			'fail'
			=> NULL,

			// an actual command i use.
			'20 10 * * * /usr/bin/php /opt/web-prod/bin/ssl-report run --email=bob@pegasusgate.net --daily'
			=> '20 10 * * * /usr/bin/php /opt/web-prod/bin/ssl-report run --email=bob@pegasusgate.net --daily'

		];

		$Input = NULL;
		$Expected = NULL;
		$Result = NULL;
		$Exceptional = NULL;

		foreach($Tests as $Input => $Expected) {
			$Exceptional = FALSE;

			////////

			try {
				$Result = Common\Struct\CrontabEntry::FromCrontab($Input);
			}

			catch(Exception $Err) {
				$Exceptional = TRUE;
			}

			////////

			if($Expected !== 'THROW') {
				$this->AssertFalse($Exceptional);
				$this->AssertEquals($Expected, $Result);
			}

			else {
				$this->AssertTrue($Exceptional);
				$this->AssertInstanceOf(Common\Error\CrontabFormatInvalid::class, $Err);
			}
		}

		return;
	}

	/** @test */
	public function
	TestCrontabFile():
	void {

		$File = new Common\Struct\CrontabFile;
		$File->Push(Common\Struct\CrontabEntry::FromCrontab('1 1 1 1 1 one'));
		$File->Push(Common\Struct\CrontabEntry::FromCrontab('2 2 2 2 2 two'));
		$File->Push(Common\Struct\CrontabEntry::FromCrontab('3 3 3 3 3 three'));
		$File->Push(Common\Struct\CrontabEntry::FromCrontab('4 4 4 4 4 four'));

		$this->AssertEquals(4, $File->Count());
		$this->AssertEquals('1 1 1 1 1 one', $File[0]->GetAsCrontab());

		return;
	}

}