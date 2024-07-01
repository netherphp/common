<?php

namespace Nether\Common;

use Nether\Atlantis;
use Nether\Common;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use DateTimeInterface;
use Stringable;
use JsonSerializable;

class Date
implements
	Stringable,
	JsonSerializable {

	const
	ConfDefaultTimezone = 'Nether.Common.Date.Timezone';

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	// we are not using the DateTimeInterface because it is missing the
	// setTimezone method depite having get timezone method. i could just
	// write it how i want but the editor claims its an error and im not
	// going to have that.

	protected DateTime|DateTimeImmutable
	$DateTime;

	protected string
	$DateFormat = Common\Values::DateFormatYMD;

	protected string
	$TimeFormat = Common\Values::DateFormatT12Z;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(mixed $Input='now', bool $Immutable=FALSE) {

		if($Input instanceof DateTimeInterface)
		$this->ConstructFromDateTime($Input, $Immutable);

		elseif($Input instanceof self)
		$this->ConstructFromSelf($Input, $Immutable);

		else
		$this->ConstructDefault($Input, $Immutable);

		return;
	}

	#[Common\Meta\Date('2023-08-11')]
	protected function
	ConstructFromSelf(self $Input, bool $Immutable):
	void {

		$DateTime = (
			$Immutable
			? new DateTimeImmutable($Input->Get(Common\Values::DateFormatYMDT24VO))
			: new DateTime($Input->Get(Common\Values::DateFormatYMDT24VO))
		);

		$this->SetDateTime($DateTime);

		return;
	}

	#[Common\Meta\Date('2023-08-11')]
	protected function
	ConstructFromDateTime(DateTimeInterface $Input, bool $Immutable):
	void {

		$DateTime = (
			$Immutable
			? new DateTimeImmutable($Input->Format(Common\Values::DateFormatYMDT24VO))
			: new DateTime($Input->Format(Common\Values::DateFormatYMDT24VO))
		);

		$this->SetDateTime($DateTime);

		return;
	}

	#[Common\Meta\Date('2023-08-11')]
	protected function
	ConstructDefault(string $Input, bool $Immutable):
	void {

		// otherwise if we are instiantiating via fuzzy input then we will
		// want to respect the immuable status to begin that chain.

		$DateTime = (
			$Immutable
			? new DateTimeImmutable($Input)
			: new DateTime($Input)
		);

		// additionally we want to change the timezone to the one we might
		// be expecting after giving it fuzz input.

		$TZ = (
			Common\Library::Get(static::ConfDefaultTimezone)
			?? 'UTC'
		);

		$this->SetDateTime($DateTime);
		$this->SetTimezone($TZ);

		return;
	}

	////////////////////////////////////////////////////////////////
	// IMPLEMENTS Stringable ///////////////////////////////////////

	public function
	__ToString():
	string {

		return $this->DateTime->Format($this->DateFormat);
	}

	////////////////////////////////////////////////////////////////
	// IMPLEMENTS Invokeable ///////////////////////////////////////

	public function
	__Invoke(...$Argv):
	?string {

		if(count($Argv) === 1)
		return $this->Get($Argv[0]);

		return $this->Get();
	}

	////////////////////////////////////////////////////////////////
	// IMPLEMENTS JsonSerializable /////////////////////////////////

	#[Common\Meta\Date('2023-08-11')]
	public function
	JsonSerialize():
	array {

		return [
			'DateTime' => $this->DateTime->Format(DateTime::RFC3339),
			'Unix'     => $this->DateTime->Format(Common\Values::DateFormatUnix),
			'Date'     => $this->DateTime->Format($this->DateFormat),
			'Time'     => $this->DateTime->Format($this->TimeFormat)
		];
	}

	////////////////////////////////////////////////////////////////
	// FEIGNS Nether\Atlantis\Prototype ////////////////////////////

	#[Common\Meta\Date('2023-08-11')]
	public function
	DescribeForPublicAPI():
	string {

		return $this->DateTime->Format(DateTime::RFC3339);
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Common\Meta\Date('2023-08-11')]
	public function
	GetDateTime():
	DateTimeInterface {

		return $this->DateTime;
	}

	#[Common\Meta\Date('2023-08-11')]
	public function
	SetDateTime(DateTimeInterface $Input):
	static {

		$this->DateTime = $Input;

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Common\Meta\Date('2022-05-04')]
	public function
	Modify(string $How):
	static {

		if($this->DateTime instanceof DateTimeImmutable)
		return new static($this->DateTime->Modify($How));

		////////

		$this->DateTime->Modify($How);
		return $this;
	}

	#[Common\Meta\Date('2023-08-12')]
	public function
	IsThisAfter(self|int $When):
	bool {

		if(is_int($When))
		$When = static::FromTime($When);

		////////

		$Here = $this->GetUnixtime();
		$Then = $When->GetUnixtime();

		if($Here < $Then)
		return FALSE;

		////////

		return TRUE;
	}

	#[Common\Meta\Date('2023-08-12')]
	public function
	IsThisBefore(self|int $When):
	bool {

		if(is_int($When))
		$When = static::FromTime($When);

		////////

		$Here = $this->GetUnixtime();
		$Then = $When->GetUnixtime();

		if($Here > $Then)
		return FALSE;

		////////

		return TRUE;
	}

	#[Common\Meta\Date('2023-08-12')]
	public function
	IsThatAfter(self|int $When):
	bool {

		return !$this->IsThisAfter($When);
	}

	#[Common\Meta\Date('2023-08-12')]
	public function
	IsThatBefore(self|int $When):
	bool {

		return !$this->IsThisBefore($When);
	}

	#[Common\Meta\Date('2023-08-11')]
	public function
	IsImmutable():
	bool {

		return ($this->DateTime instanceof DateTimeImmutable);
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Get(?string $Format=NULL):
	string {

		$Format = $Format ?? $this->DateFormat;

		return $this->DateTime->Format($Format);
	}

	public function
	GetDateFormat():
	string {

		return $this->DateFormat;
	}

	public function
	SetDateFormat(string $Format):
	static {

		$this->DateFormat = $Format;
		return $this;
	}

	public function
	GetTimeFormat():
	string {

		return $this->TimeFormat;
	}

	public function
	SetTimeFormat(string $Format):
	static {

		$this->TimeFormat = $Format;
		return $this;
	}

	public function
	GetUnixtime(bool $Normalise=TRUE):
	int {
	/*//
	@date 2023-04-17
	get the unix timestamp. by default it will normalise the value to utc
	before returning it, making this the primary method that should be used
	when storing it in a database - the library config about the default
	timezone should be used for display purposes only. as long as this method
	is used to get the timestamp for storage it should more or less be magic.
	//*/

		// it appears using the unix format flag alone is enough to have
		// the php datetime adjust for the timezone automatically without
		// the need to normalise the date. there are three unit tests
		// making sure this remains the case with various use styles.

		/*
		$Normal = new DateTime($this->DateTime->Format(DateTime::RFC822));
		$Normal->SetTimezone(new DateTimeZone('UTC'));

		return $Normal->Format(Common\Values::DateFormatUnix);
		*/

		return $this->DateTime->Format(Common\Values::DateFormatUnix);
	}

	#[Common\Meta\Date('2023-08-12')]
	public function
	GetTimezoneName():
	string {

		$TZ = $this->DateTime->GetTimezone();

		return $TZ->GetName();
	}

	#[Common\Meta\Date('2023-08-12')]
	public function
	GetTimezoneOffset():
	int {

		$TZ = $this->DateTime->GetTimezone();

		return $TZ->GetOffset($this->DateTime);
	}

	#[Common\Meta\Date('2023-08-11')]
	public function
	SetTimezone(mixed $TZ):
	static {

		if(($TZ instanceof DateTimeZone) === FALSE)
		$TZ = new DateTimeZone($TZ);

		////////

		// this is an instance where im breaking the idea of mutable on
		// purpose to be nice to use.

		$this->DateTime = $this->DateTime->SetTimezone($TZ);

		////////

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Common\Meta\Date('2022-05-04')]
	static public function
	FromDateString(string $Date, ?string $TZ=NULL, bool $Imm=FALSE):
	static {
	/*//
	consider the timezone when making an object from a date string. if a user
	passes in 2010-01-01 they are likely expecting it to be in their (or the
	site's default) timezone. without considering that might get off by one
	day type deals when rehydrating this from the unix timestamp later.
	//*/

		if($TZ === NULL)
		$TZ = (
			Common\Library::Get('Nether.Common.Date.Timezone')
			?? 'UTC'
		);

		$Output = new static(new DateTime(
			$Date, new DateTimeZone($TZ)
		), $Imm);

		return $Output;
	}

	#[Common\Meta\Date('2021-08-26')]
	static public function
	FromTime(mixed $Time, bool $Imm=FALSE):
	static {

		return new static("@{$Time} UTC", $Imm);
	}

	#[Common\Meta\Date('2023-08-11')]
	static public function
	FromDateTime(DateTimeInterface $Input):
	static {

		$Output = new static($Input);

		return $Output;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Common\Meta\Date('2023-06-01')]
	static public function
	FetchTimezoneFromSystem():
	string {

		if(PHP_OS_FAMILY === 'Windows')
		return static::FetchTimezoneFromWindows();

		return static::FetchTimezoneFromUnix();
	}

	#[Common\Meta\Date('2023-08-12')]
	static public function
	FetchTimezoneFromUnix():
	string {

		if(PHP_OS_FAMILY === 'Windows')
		return 'UTC';

		////////

		$Result = trim(`date +"%z"`);

		return $Result;
	}

	#[Common\Meta\Date('2023-08-12')]
	static public function
	FetchTimezoneFromWindows():
	string {

		if(PHP_OS_FAMILY !== 'Windows')
		return 'UTC';

		////////

		$Result = parse_ini_string(trim(
			`wmic OS Get CurrentTimeZone /value`
		));

		//if(!$Result || !isset($Result['CurrentTimeZone']))
		//return 'UTC';

		// windows returns this in minutes.

		return sprintf('%d', (
			(int)$Result['CurrentTimeZone'] / Values::MinPerHr
		));
	}

	#[Common\Meta\Date('2023-08-12')]
	static public function
	Unixtime(?string $Input=NULL):
	int {

		$Date = static::FromDateString(
			$Input ?? 'now'
		);

		return $Date->GetUnixtime();
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	/**
	 * @codeCoverageIgnore
	 */

	#[Common\Meta\Deprecated('2023-08-12', 'Use Unixtime')]
	static public function
	CurrentUnixtime():
	int {
	/*//
	@date 2023-05-31
	//*/

		$Now = new static;

		return $Now->GetUnixtime();
	}

	/**
	 * @codeCoverageIgnore
	 */

	#[Common\Meta\Deprecated('2023-08-12', 'Use IsThatAfter')]
	public function
	IsAfter(self|int $When):
	bool {

		return $this->IsThatAfter($When);
	}

	/**
	 * @codeCoverageIgnore
	 */

	#[Common\Meta\Deprecated('2023-08-12', 'Use IsThatBefore')]
	public function
	IsBefore(self|int $When):
	bool {

		return $this->IsThatBefore($When);
	}

}
