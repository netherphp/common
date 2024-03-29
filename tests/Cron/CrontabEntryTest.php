<?php

namespace NetherTestSuite\Common;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Nether\Common;

use PHPUnit\Framework\TestCase;
use Exception;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class CrontabEntryTest
extends TestCase {

	/** @test */
	public function
	TestBasic():
	void {

		$Line = Common\Struct\CrontabEntry::FromCrontab('* * * * * minutely');
		$this->AssertEquals('* * * * * minutely', $Line->GetAsCrontab());
		$this->AssertEquals('* * * * * minutely', (string)$Line);
		$this->AssertFalse($Line->IsHourly());
		$this->AssertFalse($Line->IsDaily());

		$this->AssertEquals('minutely', $Line->Command);

		$Line->SetCommand('zomg');
		$this->AssertEquals('zomg', $Line->Command);

		$Line->Reset();
		$this->AssertNull($Line->Minute);
		$this->AssertNull($Line->Hour);
		$this->AssertNull($Line->Day);
		$this->AssertNull($Line->Month);
		$this->AssertNull($Line->Weekday);
		$this->AssertNull($Line->Command);

		return;
	}

	/** @test */
	public function
	TestTimeframes():
	void {

		$Line = Common\Struct\CrontabEntry::FromCrontab('* * * * * minutely');
		$this->AssertFalse($Line->IsHourly());
		$this->AssertFalse($Line->IsDaily());

		$Line = Common\Struct\CrontabEntry::FromCrontab('0 * * * * top of the hour');
		$this->AssertTrue($Line->IsHourly());
		$this->AssertFalse($Line->IsDaily());

		$Line = Common\Struct\CrontabEntry::FromCrontab('* 0 * * * top of the day');
		$this->AssertFalse($Line->IsHourly());
		$this->AssertTrue($Line->IsDaily());

		$Line = Common\Struct\CrontabEntry::FromCrontab('* * 0 * * top of the month');
		$this->AssertFalse($Line->IsHourly());
		$this->AssertFalse($Line->IsDaily());

		$Line = Common\Struct\CrontabEntry::FromCrontab('* * * 0 * top of the year');
		$this->AssertFalse($Line->IsHourly());
		$this->AssertFalse($Line->IsDaily());

		$Line = Common\Struct\CrontabEntry::FromCrontab('* * * * 0 top of the week');
		$this->AssertFalse($Line->IsHourly());
		$this->AssertFalse($Line->IsDaily());

		return;
	}

	/** @test */
	public function
	TestTimerMethods():
	void {

		Common\Library::Set(Common\Date::ConfDefaultTimezone, 'UTC');

		$Now = new Common\Date('now', TRUE);
		$Future = $Now->Modify('+1 hour');

		// test the path where the time is in the future and bails early.

		$Line = Common\Struct\CrontabEntry::FromCrontab(sprintf(
			'%1$d %2$d %3$d %4$d * minutely',
			$Future->Get('i'),
			$Future->Get('H'),
			$Future->Get('d'),
			$Future->Get('m')
		));

		$Date = $Line->GetTimerAsObject();

		$this->AssertEquals(
			$Date->Get(Common\Values::DateFormatYMDT24VZ),
			$Line->GetTimerAsWords()
		);

		// test the path where it bumps to the next minute.

		$Line = Common\Struct\CrontabEntry::FromCrontab('* * * * * minutely');
		$Date = $Line->GetTimerAsObject();

		$this->AssertEquals(
			$Date->Get(Common\Values::DateFormatYMDT24VZ),
			$Line->GetTimerAsWords()
		);

		// test the path where it bumps to the next hour.

		$Line = Common\Struct\CrontabEntry::FromCrontab('0 * * * * hourly');
		$Date = $Line->GetTimerAsObject();

		$this->AssertEquals(
			$Date->Get(Common\Values::DateFormatYMDT24VZ),
			$Line->GetTimerAsWords()
		);

		// test the path where it bumps to the next hour.

		$Line = Common\Struct\CrontabEntry::FromCrontab('0 * * * * hourly');
		$Date = $Line->GetTimerAsObject();

		$this->AssertEquals(
			$Date->Get(Common\Values::DateFormatYMDT24VZ),
			$Line->GetTimerAsWords()
		);

		// test the path where it bumps to the next day.

		$Line = Common\Struct\CrontabEntry::FromCrontab('0 0 * * * daily');
		$Date = $Line->GetTimerAsObject();

		$this->AssertEquals(
			$Date->Get(Common\Values::DateFormatYMDT24VZ),
			$Line->GetTimerAsWords()
		);

		// test the path where it bumps to the next month.

		$Line = Common\Struct\CrontabEntry::FromCrontab('0 0 0 * * monthly');
		$Date = $Line->GetTimerAsObject();

		$this->AssertEquals(
			$Date->Get(Common\Values::DateFormatYMDT24VZ),
			$Line->GetTimerAsWords()
		);

		// test the int method.

		$Time = $Line->GetTimerAsInt();
		$Frame = $Line->GetTimerAsTimeframe();

		$this->AssertEquals($Date->GetUnixtime(), $Time);
		$this->AssertTrue(strlen($Frame->Get()) > 0);

		Common\Library::Set(Common\Date::ConfDefaultTimezone, NULL);
		return;
	}

	/** @test */
	public function
	TestCrontabValue():
	void {

		$this->AssertEquals('*', Common\Struct\CrontabEntry::CrontabValue(NULL));
		$this->AssertEquals('*', Common\Struct\CrontabEntry::CrontabValue('*'));
		$this->AssertEquals('1', Common\Struct\CrontabEntry::CrontabValue(1));
		$this->AssertEquals('1', Common\Struct\CrontabEntry::CrontabValue('1'));

		$this->AssertEquals('daily', Common\Struct\CrontabEntry::CrontabValue('daily'));

		return;
	}

}
