<?php

namespace Nether\Common;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Exception;
use Iterator;
use JsonSerializable;
use ReturnTypeWillChange;
use Serializable;
use SplFileInfo;

////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////

#[Meta\Date('2015-12-02')]
class Datastore
implements
	ArrayAccess,
	Countable,
	Iterator,
	JsonSerializable {

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	const
	FormatPHP  = 1,
	FormatJSON = 2;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected ?string
	$Title = NULL;

	protected ?string
	$Filename = NULL;

	protected int
	$Format = self::FormatPHP;

	protected bool
	$PrettyJSON = TRUE;

	protected bool
	$FullJSON = FALSE;

	protected bool
	$FullDebug = FALSE;

	protected bool
	$FullSerialize = FALSE;

	protected mixed
	$Sorter = NULL;

	protected array
	$ProtectedKeys = [];

	protected array
	$Data = [];

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Meta\Date('2015-12-02')]
	public function
	__Construct(?iterable $Input=NULL) {

		if($Input !== NULL)
		$this->SetData($Input);

		$this->OnPrepare();
		$this->OnReady();

		return;
	}

	#[Meta\Date('2015-12-02')]
	public function
	__DebugInfo():
	array {

		if($this->FullDebug)
		return (array)$this;

		////////

		$Output = [];
		$Key = NULL;
		$Val = NULL;
		$Type = NULL;

		foreach($this->Data as $Key => $Val) {
			if(array_key_exists($Key, $this->ProtectedKeys)) {

				// TRUE = keep key obfus value
				// FALSE = omit key completely.

				if($this->ProtectedKeys[$Key] === FALSE)
				continue;

				////////

				$Type = gettype($Val);
				$Val = match($Type) {
					'string'
					=> sprintf(
						'[protected %s len:%d]',
						$Type, strlen($Val)
					),

					default
					=> sprintf('[protected %s]', $Type)
				};
			}

			$Output[$Key] = $Val;
		}

		return $Output;
	}

	#[Meta\Date('2015-12-02')]
	public function
	__Invoke():
	array {

		return $this->Data;
	}

	#[Meta\Date('2023-11-08')]
	protected function
	OnPrepare():
	void {

		return;
	}

	#[Meta\Date('2023-11-08')]
	protected function
	OnReady():
	void {

		return;
	}

	////////////////////////////////////////////////////////////////
	// IMPLEMENTS Serializable /////////////////////////////////////

	#[Meta\Date('2023-08-19')]
	public function
	__Serialize():
	array {

		$Output = NULL;
		$Value = NULL;

		// bubble down the serialize setting to any substores.

		foreach($this->Data as $Value) {
			if($Value instanceof self)
			$Value->SetFullSerialize($this->FullSerialize);
		}

		// handle if we want a small serialize.

		if(!$this->FullSerialize) {
			$Output = [ 'Data' => $this->Data ];

			if($this->Title)
			$Output['Title'] = $this->Title;

			if($this->Filename) {
				$Output['Filename'] = $this->Filename;
				$Output['Format'] = $this->Format;
			}

			return $Output;
		}

		// or the full serialize.

		return (array)$this;
	}

	#[Meta\Date('2023-08-19')]
	public function
	__Unserialize(array $Input):
	void {

		$Key = NULL;
		$Value = NULL;

		foreach($Input as $Key => $Value)
		$this->{ltrim($Key, "\0*\0")} = $Value;

		return;
	}

	////////////////////////////////////////////////////////////////
	// IMPLEMENTS JsonSerializable /////////////////////////////////

	#[Meta\Date('2021-08-18')]
	public function
	JsonSerialize():
	mixed {

		if(!$this->FullJSON)
		return $this->Data;

		return $this;
	}

	////////////////////////////////////////////////////////////////
	// IMPLEMENTS Countable ////////////////////////////////////////

	public function
	Count():
	int {
	/*//
	@date 2015-12-02
	count how many items are in this datastore.
	//*/

		return count($this->Data);
	}

	////////////////////////////////////////////////////////////////
	// IMPLEMENTS ArrayAccess //////////////////////////////////////

	#[Meta\Date('2015-12-02')]
	public function
	OffsetExists(mixed $Key):
	bool {

		return array_key_exists($Key, $this->Data);
	}

	#[Meta\Date('2015-12-02')]
	public function
	OffsetGet(mixed $Key):
	mixed {

		if(array_key_exists($Key, $this->Data))
		return $this->Data[$Key];

		return NULL;
	}

	#[Meta\Date('2015-12-02')]
	public function
	OffsetSet(mixed $Key, mixed $Value):
	void {

		// enables $Dataset[] = 'val';

		if($Key === NULL)
		$this->Data[] = $Value;

		// enables $Dataset['key'] = 'val';

		else
		$this->Data[$Key] = $Value;

		return;
	}

	#[Meta\Date('2015-12-02')]
	public function
	OffsetUnset($Key):
	void {

		unset($this->Data[$Key]);

		return;
	}

	////////////////////////////////////////////////////////////////
	// IMPLEMENTS Iterator /////////////////////////////////////////

	#[Meta\Date('2015-12-02')]
	public function
	Current():
	mixed {

		return current($this->Data);
	}

	#[Meta\Date('2015-12-02')]
	public function
	Key():
	int|string {

		return key($this->Data);
	}

	#[Meta\Date('2015-12-02')]
	#[ReturnTypeWillChange]
	public function
	Next():
	void {

		next($this->Data);

		return;
	}

	#[Meta\Date('2015-12-02')]
	#[ReturnTypeWillChange]
	public function
	Rewind():
	void {

		reset($this->Data);

		return;
	}

	#[Meta\Date('2015-12-02')]
	public function
	Valid():
	bool {

		return (key($this->Data) !== NULL);
	}

	////////////////////////////////////////////////////////////////
	// implements IteratorAggregate ////////////////////////////////

	public function
	GetIterator():
	ArrayIterator {

		// return an iterator that will only process the data if it
		// actually gets ran over.

		return new ArrayIterator(array_map(
			(fn(mixed $Key)=> $this->Get($Key)),
			array_combine(
				array_keys($this->Data),
				array_keys($this->Data)
			)
		));
	}

	////////////////////////////////////////////////////////////////
	// Protected Key API ///////////////////////////////////////////

	// just a way to provide for not accidentally var_dumping all your
	// database secrets or whatever. it only really has effect for the
	// magic debug method info only, it will not stop you from straight up
	// asking for and then echoing something you should not have.

	public function
	Protect(string|array $Key, bool $Omit=FALSE):
	static {

		if(is_array($Key)) {
			$K = NULL;

			foreach($Key as $K)
			$this->ProtectedKeys[$K] = !$Omit;
		}

		else {
			$this->ProtectedKeys[$Key] = !$Omit;
		}

		return $this;
	}

	public function
	Expose(string|array|bool $Key):
	static {

		if(is_array($Key)) {
			$K = NULL;

			foreach($Key as $K) {
				if(array_key_exists($K, $this->ProtectedKeys))
				unset($this->ProtectedKeys[$K]);
			}
		}

		elseif(is_string($Key)) {
			if(array_key_exists($Key, $this->ProtectedKeys))
			unset($this->ProtectedKeys[$Key]);
		}

		elseif($Key === TRUE) {
			unset($this->ProtectedKeys);
			$this->ProtectedKeys = [];
		}

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Meta\Date('2015-12-02')]
	public function
	GetData():
	array {

		return $this->Data;
	}

	#[Meta\Date('2022-11-23')]
	public function
	&GetDataRef():
	array {

		// return the dataset by reference. keep in mind that you need to
		// do the ampersand on the reciever end too, and that it only
		// really works if assigned to a variable first. dropping this on
		// an array function for example wont work.

		return $this->Data;
	}

	#[Meta\Date('2015-12-02')]
	public function
	SetData(?iterable $Input):
	static {

		// fun fact:
		// in 8.2.0 iterator_to_array can handle arrays without needing
		// to check for ourselves.

		$this->Data = match(TRUE) {
			(is_array($Input) === FALSE)
			=> iterator_to_array($Input),

			(is_array($Input) === TRUE)
			=> $Input,

			default
			=> [ ]
		};

		return $this;
	}

	#[Meta\Date('2015-12-02')]
	public function
	GetFilename():
	?string {

		return $this->Filename;
	}

	#[Meta\Date('2015-12-02')]
	public function
	SetFilename(string $Filename):
	static {

		$this->Filename = $Filename;
		$this->Format = $this->DetermineFormatByFilename($Filename);

		return $this;
	}

	#[Meta\Date('2015-12-02')]
	public function
	GetFormat():
	int {

		return $this->Format;
	}

	#[Meta\Date('2015-12-02')]
	public function
	SetFormat(int $Format):
	static {

		$this->Format = match($Format) {
			static::FormatJSON,
			static::FormatPHP
			=> $Format,

			default
			=> static::FormatPHP
		};

		return $this;
	}

	#[Meta\Date('2021-08-18')]
	public function
	GetFullDebug():
	bool {

		return $this->FullDebug;
	}

	#[Meta\Date('2021-08-18')]
	public function
	SetFullDebug(bool $Enable):
	static {

		$this->FullDebug = $Enable;

		return $this;
	}

	#[Meta\Date('2021-08-18')]
	public function
	GetFullJSON():
	bool {

		return $this->FullJSON;
	}

	#[Meta\Date('2021-08-18')]
	public function
	SetFullJSON(bool $Enable):
	static {

		$this->FullJSON = $Enable;

		return $this;
	}

	#[Meta\Date('2021-08-18')]
	public function
	GetFullSerialize():
	bool {

		return $this->FullSerialize;
	}

	#[Meta\Date('2021-08-18')]
	public function
	SetFullSerialize(bool $Enable):
	static {

		$this->FullSerialize = $Enable;

		return $this;
	}

	#[Meta\Date('2016-02-25')]
	public function
	GetSorter():
	mixed {

		return $this->Sorter;
	}

	#[Meta\Date('2016-02-25')]
	public function
	SetSorter(?callable $Fn):
	static {

		$this->Sorter = $Fn;

		return $this;
	}

	#[Meta\Date('2016-03-25')]
	public function
	GetTitle():
	?string {

		return $this->Title;
	}

	#[Meta\Date('2016-03-25')]
	public function
	SetTitle(?string $Title=''):
	static {

		$this->Title = $Title;

		return $this;
	}

	#[Meta\Date('2022-08-15')]
	public function
	DetermineFormatByFilename(string $Filename):
	int {

		// if the filename matches these specific types it will return
		// what we think it should be. else it will return what it already
		// is in the event you just doing whatever you want.

		$File = strtolower($Filename);

		////////

		if(str_ends_with($File, '.json'))
		return static::FormatJSON;

		if(str_ends_with($File, '.phson'))
		return static::FormatPHP;

		////////

		return $this->Format;
	}

	////////////////////////////////////////////////////////////////
	// General API /////////////////////////////////////////////////

	#[Meta\Date('2016-12-02')]
	public function
	Each(callable $Function, ?array $Argv=NULL):
	static {

		$Key = NULL;
		$Value = NULL;

		foreach($this->Data as $Key => &$Value)
		$Function($Value, $Key, $this, ...($Argv??[]));

		return $this;
	}

	#[Meta\Date('2023-11-15')]
	#[Meta\Info('For when you dun wanna get fucked about on what the callback arg order is.')]
	public function
	EachKeyValue(callable $Function, ?iterable $Argv=NULL):
	static {

		$Key = NULL;
		$Value = NULL;

		foreach($this->Data as $Key => &$Value)
		$Function($Key, $Value, $this, ...($Argv??[]));

		return $this;
	}

	public function
	Get(mixed $Key):
	mixed {
	/*//
	@date 2015-12-02
	returns the data by the specified key name. if data with that key did not
	exist then it will return null. keep this in mind if you are also
	inserting nulls into the dataset.
	//*/

		if(array_key_exists($Key,$this->Data))
		return $this->Data[$Key];

		return NULL;
	}

	public function
	&GetRef(mixed $Key):
	mixed {
	/*//
	@date 2015-12-02
	works the same as Get but instead returns a reference to the data so you
	can manipulate non-objects if needed.
	//*/

		if(array_key_exists($Key,$this->Data))
		return $this->Data[$Key];

		return throw new Exception(
			'unable to give you a reference to data that does not exist '.
			'in this case you really should be Has\'ing first if you '.
			'insist on doing this silly hacky stuff.'
		);
	}

	public function
	GetFirstKey():
	mixed {
	/*//
	@date 2021-09-13
	get what the first key in this dataset is.
	//*/

		return array_key_first($this->Data);
	}

	public function
	GetLastKey():
	mixed {
	/*//
	@date 2021-09-13
	get what the last key in this dataset is.
	//*/

		return array_key_last($this->Data);
	}

	public function
	HasKey(mixed $Key):
	bool {
	/*//
	@date 2015-12-02
	returns if this datastore has the requested key.
	//*/

		return array_key_exists($Key,$this->Data);
	}

	public function
	HasValue(mixed $Val, bool $Strict=FALSE):
	bool {
	/*//
	@date 2015-12-02
	returns if this datastore has the requested value. if the value is found
	it will return the key that contains it. if not found it will return a
	boolean false.
	//*/

		return array_search($Val, $this->Data, $Strict) !== FALSE;
	}

	#[Meta\Date('2023-12-05')]
	public function
	HasAnyKey(...$Keys):
	bool {

		$Key = NULL;

		foreach($Keys as $Key) {
			if(is_string($Key) || is_int($Key))
			if($this->HasKey($Key))
			return TRUE;
		}

		return FALSE;
	}

	#[Meta\Date('2023-12-05')]
	public function
	HasAnyValue(...$Vals):
	bool {

		$Val = NULL;

		foreach($Vals as $Val) {
			if($this->HasValue($Val))
			return TRUE;
		}

		return FALSE;
	}

	public function
	IsFirstKey(mixed $Key):
	bool {
	/*//
	@date 2021-09-13
	ask if this is the first key.
	//*/

		return ($Key === array_key_first($this->Data));
	}

	public function
	IsLastKey(mixed $Key):
	bool {
	/*//
	@date 2021-09-13
	ask if this is the last key.
	//*/

		return ($Key === array_key_last($this->Data));
	}

	public function
	Keys():
	array {
	/*//
	@date 2021-09-20
	//*/

		return array_keys($this->Data);
	}

	public function
	Values(bool $Array=FALSE):
	array|static {
	/*//
	@date 2021-01-05
	fetches a clean indexed copy of the data via array_values.
	//*/

		if(!$Array)
		return new static(array_values($this->Data));

		return array_values($this->Data);
	}

	public function
	IsTrue(string $Key, bool $NullIsTrue=FALSE):
	bool {
	/*//
	@date 2022-08-31
	check if the data is true or trueable, and handle if undefined values
	should be treated as true or not.
	//*/

		$Val = $this->Get($Key);

		// if the value was null in the context of a configuration file
		// you may want undefined keys to be treated as true or false
		// depending on your desired default behaviours.

		if($Val === NULL)
		$Val = $NullIsTrue ? TRUE : FALSE;

		// if the value was a string we will only accept true and TRUE
		// as truth. everything else will be false to avoid php being
		// flippity floppy based on the first character.

		if(is_string($Val))
		$Val = match($Val) {
			'true', 'TRUE'
			=> TRUE,

			default
			=> FALSE
		};

		////////

		$Val = (bool)$Val;

		return $Val;
	}

	public function
	IsTrueEnough(string $Key):
	bool {
	/*//
	@date 2022-08-31
	check if data is true and consider undefined keys as true.
	//*/

		return $this->IsTrue($Key, TRUE);
	}

	public function
	IsFalse(string $Key, bool $NullIsTrue=FALSE):
	bool {
	/*//
	@date 2022-08-31
	inversion of IsTrue lmao.
	//*/

		return !$this->IsTrue($Key, $NullIsTrue);
	}

	public function
	IsFalseEnough(string $Key):
	bool {
	/*//
	@date 2022-08-31
	check if data is false and consider undefined keys as false.
	//*/

		return !$this->IsTrue($Key, FALSE);
	}

	public function
	IsNull(string $Key):
	bool {
	/*//
	@date 2022-08-31
	check if data is null or nullable. this could be either a literal null
	or undefined. if you need to know if it legit exists or not there is
	the HasKey method.
	//*/

		$Val = $this->Get($Key);

		////////

		if(is_string($Val))
		if($Val === 'null' || $Val === 'NULL')
		$Val = NULL;

		////////

		return ($Val === NULL);
	}

	#[Meta\Date('2023-11-08')]
	#[Meta\Info('Returns if the array numerically indexed.')]
	public function
	IsList():
	bool {

		return array_is_list($this->Data);
	}

	////////////////////////////////////////////////////////////////
	// Manipulation API ////////////////////////////////////////////

	public function
	Accumulate(mixed $Initial, callable $Function):
	mixed {
	/*//
	@date 2021-04-15
	pass the initial value through a chained callable game and return the
	resulting value.
	//*/

		// i absolutely loath that its called array_reduce when at no point
		// does it reduce the data set. in reality what this function does
		// is play the telephone game with an initial value. that is why
		// it is called accumulate here.

		return array_reduce($this->Data,$Function,$Initial);
	}

	#[Meta\Date('2024-01-02')]
	#[Meta\Info('Accumulate returning a Datastore compiled from the callback which should return a Datastore.')]
	public function
	Compile(callable $Func):
	static {

		return $this->Accumulate((new static), $Func);
	}

	public function
	Bump(string $Key, int|float $Inc=1):
	static {

		$this->Inc($Key, $Inc);

		return $this;
	}

	public function
	Inc(string $Key, int|float $Inc=1):
	int|float {

		$Val = match(TRUE) {
			is_int($this->Data[$Key]),
			is_float($this->Data[$Key])
			=> $this->Data[$Key],

			default
			=> 0
		};

		$Val += $Inc;

		$this->Data[$Key] = $Val;

		return $Val;
	}

	public function
	Clear():
	static {
	/*//
	@date 2016-03-19
	dump the old dataset to start fresh. syntaxual sugar instead of having to
	use $Store->SetData([]);
	//*/

		unset($this->Data);
		$this->Data = [];

		return $this;
	}

	public function
	Define(string $Key, mixed $Val):
	static {
	/*//
	@date 2022-08-29
	add this data under this key, but only if it does not already exist.
	the "do not overwrite" version of Shove.
	//*/

		if(!array_key_exists($Key, $this->Data))
		$this->Data[$Key] = $Val;

		return $this;
	}

	public function
	Distill(callable $FilterFunc):
	Datastore {
	/*//
	@date 2020-10-22
	return a new datastore of the result of an array filter.
	//*/

		return new static(
			array_filter(
				$this->Data,
				$FilterFunc,
				ARRAY_FILTER_USE_BOTH
			)
		);
	}

	public function
	Filter(callable $FilterFunc):
	self {
	/*//
	@date 2020-05-27
	alter the current dataset with the result of an array filter.
	//*/

		$this->Data = array_filter(
			$this->Data,
			$FilterFunc,
			ARRAY_FILTER_USE_BOTH
		);

		return $this;
	}

	public function
	Join(string $Delimiter=' '):
	string {
	/*//
	@date 2022-07-29
	join and return the dataset together with the specified delimiter.
	note you should probably map or remap it to values that you know will
	actually be joinable prior.
	//*/

		return join($Delimiter, $this->Data);
	}

	public function
	Map(callable $FilterFunc):
	Datastore {
	/*//
	@date 2020-05-27
	return a new datastore of the result of an array map.
	//*/

		return new static(array_map(
			$FilterFunc,
			$this->Data
		));
	}

	public function
	MapKeys(callable $Func):
	Datastore {
	/*//
	@date 2021-09-20
	same as RemapKeys except it returns a new datastore of the result.
	//*/

		$Output = [];
		$Result = NULL;
		$Key = NULL;
		$Val = NULL;

		foreach($this->Data as $Key => $Val) {
			$Result = $Func($Key,$Val,$this);

			if(is_array($Result))
			$Output[key($Result)] = current($Result);
		}

		return new static($Output);
	}

	public function
	Pop():
	mixed {
	/*//
	@date 2015-12-02
	return and remove the last value on the array. if the array is empty it
	will return null. keep this in mind if you are also inserting nulls into
	the dataset.
	//*/

		return array_pop($this->Data);
	}

	public function
	Push(mixed $Value, mixed $Key=NULL):
	static {
	/*//
	@date 2015-12-02
	appends the specified item to the end of the dataset. if a key is
	specified and a data for that key already existed, then it will be
	overwritten with the new data.
	//*/

		if($Key === NULL)
		$this->Data[] = $Value;

		else
		$this->Data[$Key] = $Value;

		return $this;
	}

	public function
	Reindex():
	static {
	/*//
	@date 2015-12-02
	reindex the data array to remove gaps in the numeric keys while still
	preserving any string keys that existed.
	//*/

		$Key = NULL;
		$Value = NULL;
		$Data = [];

		foreach($this->Data as $Key => $Value) {
			if(is_int($Key)) $Data[] = $Value;
			else $Data[$Key] = $Value;
		}

		$this->Data = $Data;
		return $this;
	}

	public function
	RemapKeys(callable $Func):
	static {
	/*//
	@date 2021-09-20
	rekey and remap this dataset by returning an array with a single
	element from the callback. the key of the result is where the data will
	be moved to, with the data of the result being said data. if false or
	null is returned instead, it will also filter that item out of the
	array. modifies this datastore.

	based on a gist by jasand found randomly one day.
	https://gist.github.com/jasand-pereza/84ecec7907f003564584
	//*/

		$Output = [];
		$Result = NULL;
		$Key = NULL;
		$Val = NULL;

		foreach($this->Data as $Key => $Val) {
			$Result = $Func($Key, $Val, $this);

			if(is_array($Result))
			$Output[key($Result)] = current($Result);
		}

		$this->SetData($Output);

		return $this;
	}

	public function
	Remap(callable $FilterFunc):
	static {
	/*//
	@date 2020-05-27
	alter the current dataset using the array_map filtering.
	//*/

		$this->Data = array_map($FilterFunc,$this->Data);

		return $this;
	}

	public function
	Remove(mixed $Key):
	static {
	/*//
	@date 2015-12-02
	removes the data by the specified key name if it exists. if not then
	nothing happens and you can just go on your way.
	//*/

		if(array_key_exists($Key,$this->Data))
		unset($this->Data[$Key]);

		return $this;
	}

	public function
	Revalue():
	static {
	/*//
	@date 2021-01-05
	rebuilds the dataset with clean indexes via array_values.
	//*/

		$this->Data = array_values($this->Data);
		return $this;
	}

	public function
	Reverse():
	static {
	/*//
	@date 2023-04-20
	quick flip of the ordering.
	//*/

		$this->Data = array_reverse($this->Data);

		return $this;
	}

	public function
	Set(mixed $Key, mixed $Value):
	static {
	/*//
	@date 2022-08-29
	alias for shove. the two methods may eventually flip flop with one of them
	becoming deprecated idk yet. shove makes sense in some contexts.
	//*/

		return $this->Shove($Key, $Value);
	}

	public function
	Shove(mixed $Key, mixed $Value):
	static {
	/*//
	@date 2015-12-02
	append the specified item to the end of the dataset. if the key already
	exists then the original data will be overwritten in the same place. same
	principal as Push, but syntaxally makes more sense when dealing with
	associative data.
	//*/

		$this->Data[$Key] = $Value;
		return $this;
	}

	public function
	Shuffle():
	static {
	/*//
	@date 2021-02-22
	randomize the array in-place.
	//*/

		shuffle($this->Data);
		return $this;
	}

	public function
	Shift():
	mixed {
	/*//
	@date 2015-12-02
	performs a standard array shifting operation returning whatever slide off
	the front of the dataset.
	//*/

		return array_shift($this->Data);
	}

	public function
	Sort(?callable $Function=NULL):
	static {
	/*//
	@date 2015-12-02
	sort the dataset. if no custom function is supplied it will execute the
	default sorter function. if there is no default sorter function it will
	execute the php asort function.
	//*/

		if($Function === NULL) {
			if(is_callable($this->Sorter))
			uasort($this->Data, $this->Sorter);

			else
			asort($this->Data);
		}

		////////

		if(is_callable($Function))
		uasort($this->Data, $Function);

		////////

		return $this;
	}

	public function
	SortKeys(?callable $Function=NULL):
	static {
	/*//
	@date 2022-11-23
	behaves the same as Sort except against the keys.
	//*/

		if($Function === NULL) {
			if(is_callable($this->Sorter))
			uksort($this->Data, $this->Sorter);

			else
			ksort($this->Data);
		}

		////////

		if(is_callable($Function))
		uksort($this->Data, $Function);

		return $this;
	}

	public function
	Unshift(mixed $Val):
	static {
	/*//
	@date 2015-12-02
	performs a standard array unshifting operation, shoving the specified value
	onto the front of the array.
	//*/

		array_unshift($this->Data,$Val);
		return $this;
	}

	#[Meta\Date('2023-11-11')]
	#[Meta\Info('Remove duplicates from this dataset.')]
	public function
	Flatten():
	static {

		$this->Data = array_unique($this->Data);

		return $this;
	}

	#[Meta\Date('2023-11-11')]
	#[Meta\Info('Make a new dataset with just the unique items.')]
	public function
	Unique():
	static {

		return new static(array_unique($this->Data));
	}

	#[Meta\Date('2023-11-15')]
	public function
	MapKeyValue(callable $Fn, ...$Argv):
	static {

		// this is going to be absolutely mental on large datasets. but it
		// nets me the api i want and all that stuff is just a handful of
		// stuffs so get rekt.

		$Sigh = function(mixed $Val, mixed $Key) use($Fn, $Argv) {
			return $Fn($Key, $Val, ...$Argv);
		};

		return new static(array_combine(
			array_keys($this->Data),
			array_map($Sigh, $this->Data, array_keys($this->Data))
		));
	}

	#[Meta\Date('2023-11-15')]
	public function
	RemapKeyValue(callable $Fn, ...$Argv):
	static {

		// see MapKeyValue for the rant.

		$Sigh = function(mixed $Val, mixed $Key) use($Fn, $Argv) {
			return $Fn($Key, $Val, ...$Argv);
		};

		$this->Data = array_combine(
			array_keys($this->Data),
			array_map($Sigh, $this->Data, array_keys($this->Data))
		);

		return $this;
	}

	#[Meta\Date('2023-11-15')]
	public function
	HeadCrop(int $Len=1):
	static {

		$this->Data = array_slice(
			$this->Data,
			0, $Len,
			!array_is_list($this->Data)
		);

		return $this;
	}

	#[Meta\Date('2023-11-15')]
	public function
	HeadPush(mixed $Item):
	static {

		array_unshift($this->Data, $Item);

		return $this;
	}

	#[Meta\Date('2023-11-15')]
	public function
	HeadPop():
	mixed {

		return array_shift($this->Data);
	}

	#[Meta\Date('2023-11-15')]
	public function
	TailCrop(int $Len=1):
	static {

		$Start = (count($this->Data) - $Len);

		$this->Data = array_slice(
			$this->Data,
			$Start, $Len,
			!array_is_list($this->Data)
		);

		return $this;
	}

	#[Meta\Date('2023-11-15')]
	public function
	TailPush(mixed $Item):
	static {

		array_push($this->Data, $Item);

		return $this;
	}

	#[Meta\Date('2023-11-15')]
	public function
	TailPop():
	mixed {

		return array_pop($this->Data);
	}

	#[Meta\Date('2023-11-17')]
	public function
	Copy():
	static {

		// this is a method to do a thing i do very often as i still
		// am genuinely unsure how deep clone() even is anyway.

		// but it can also be considered placeholder for what i am
		// considering may become what need to be like the deepest of
		// copies. maybe an arg deep=true.

		// the main reason i am considering a deep copy is when i want
		// a clear clean cut copy. copy on write works great and all that
		// but there are times where i just want, both in my head and in
		// my digital hand, something with zero magic strings attached.

		return $this->Map(fn(mixed $D)=> $D);
	}

	#[Meta\Date('2023-11-17')]
	public function
	Flip():
	static {

		$this->Data = array_flip($this->Data);

		return $this;
	}

	#[Meta\Date('2024-04-03')]
	public function
	Slice(int $Offset, ?int $Length=NULL):
	static {

		return new static(array_slice(
			$this->Data,
			$Offset,
			$Length,
			FALSE
		));
	}

	#[Meta\Date('2024-04-03')]
	public function
	Chop(int $Offset, ?int $Length=NULL):
	static {

		$this->Data = array_slice(
			$this->Data,
			$Offset,
			$Length,
			FALSE
		);

		return $this;
	}

	////////////////////////////////////////////////////////////////
	// Merging API /////////////////////////////////////////////////

	public function
	MergeRight(array|object $Input):
	static {
	/*//
	@date 2016-03-18
	appends the input to the dataset. if there are conflicting assoc keys, the
	input data here will override whatever already existed. numeric keys will
	be appended no matter what. all new data will appear at the end of the
	array. any data that had conflicting assoc keys will remain in the
	sequence position that it was already in.
	//*/

		if($Input instanceof static)
		$Input = $Input->GetData();

		elseif(is_object($Input))
		$Input = (array)$Input;

		////////

		$this->Data = array_merge(
			$this->Data,
			$Input
		);

		return $this;
	}

	public function
	MergeLeft(array|object $Input):
	static {
	/*//
	@date 2016-03-18
	works like MergeRight except it appears your data was added to the
	beginning of the dataset.
	//*/

		if($Input instanceof static)
		$Input = $Input->GetData();

		elseif(is_object($Input))
		$Input = (array)$Input;

		////////

		$this->Data = array_reverse(array_merge(
			array_reverse($this->Data),
			array_reverse($Input)
		));

		return $this;
	}

	public function
	BlendRight(array|object $Input):
	static {
	/*//
	@date 2016-03-18
	works the same as MergeRight, only instead of your input overwriting the
	original data will be kept. your new data will appear at the end of the
	array, and the original data will maintain its original location. numeric
	keys will flat out be appended with the next numeric in the sequence just
	like array_merge.
	//*/

		$Key = NULL;
		$Val = NULL;

		////////

		if($Input instanceof static)
		$Input = $Input->GetData();

		elseif(is_object($Input))
		$Input = (array)$Input;

		////////

		foreach($Input as $Key => $Val) {
			if(is_int($Key)) {
				$this->Data[] = $Val;
				continue;
			}

			if(!array_key_exists($Key,$this->Data)) {
				$this->Data[$Key] = $Val;
				continue;
			}
		}

		return $this;
	}

	public function
	BlendLeft(array|object $Input):
	static {
	/*//
	@date 2016-03-18
	works the same as BlendRight, only it appears your data was added to
	the beginning to the dataset.
	//*/

		$Key = NULL;
		$Val = NULL;

		////////

		if($Input instanceof static)
		$Input = $Input->GetData();

		elseif(is_object($Input))
		$Input = (array)$Input;

		////////

		$this->Data = array_reverse($this->Data);

		foreach(array_reverse($Input) as $Key => $Val) {
			if(is_int($Key)) {
				$this->Data[] = $Val;
				continue;
			}

			if(!array_key_exists($Key,$this->Data)) {
				$this->Data[$Key] = $Val;
				continue;
			}
		}

		$this->Data = array_reverse($this->Data);
		return $this;
	}

	////////////////////////////////////////////////////////////////
	// File Operations /////////////////////////////////////////////

	public function
	Read(?string $Filename=NULL, bool $Append=FALSE):
	static {
	/*//
	@date 2015-12-02
	//*/

		$Filename ??= $Filename ?? $this->Filename;

		if(!$Filename)
		throw new Error\FileNotSpecified;

		////////

		$File = new SplFileInfo($Filename);
		$Basename = $File->GetBasename();
		$Ext = strtolower($File->GetExtension()) ?: NULL;

		if(!file_exists($Filename))
		throw new Error\FileNotFound($Basename);

		if(!$File->IsReadable())
		throw new Error\FileUnreadable($Basename);

		////////

		$Data = NULL;

		if($Ext === 'json' || $this->Format === static::FormatJSON)
		$Data = json_decode(file_get_contents($Filename), TRUE);
		else
		$Data = unserialize(file_get_contents($Filename));

		if(!is_array($Data))
		$Data = (array)$Data;

		////////

		if(!$Append)
		$this->Data = $Data;
		else
		$this->Data = array_merge($this->Data, $Data);

		return $this;
	}

	public function
	Write(?string $Filename=NULL):
	static {
	/*//
	@date 2015-12-02
	write this datastructure to disk.
	//*/

		$Filename ??= $Filename ?? $this->Filename;

		if(!$Filename)
		throw new Error\FileNotSpecified;

		////////

		$File = new SplFileInfo($Filename);
		$Val = NULL;

		$Dirname = $File->GetPath();
		$Format = $this->Format;
		$FlagsJSON = 0;
		$Data = NULL;

		if($this->Filename !== $Filename)
		$Format = $this->DetermineFormatByFilename($Filename);

		if($this->PrettyJSON)
		$FlagsJSON |= JSON_PRETTY_PRINT;

		////////

		if(!file_exists($Filename)) {
			if(!is_dir($Dirname) && !@mkdir($Dirname, 0777, TRUE))
			throw new Error\DirUnwritable(dirname($Dirname));

			elseif(!is_writable($Dirname))
			throw new Error\DirUnwritable($Dirname);
		}

		else {
			if(!is_writable($Filename))
			throw new Error\FileUnwritable($Filename);
		}

		////////

		foreach($this->Data as $Val)
		if($Val instanceof self)
		$Val->SetFullSerialize($this->FullSerialize);

		////////

		$Data = new Overbuffer;

		switch($Format) {
			case static::FormatJSON:
				$Data->Exec(fn()=> print( json_encode($this->Data, $FlagsJSON)) );
			break;
			default:
				$Data->Exec(fn()=> print( serialize($this->Data)) );
			break;
		}

		$Data
		->Filter(Filters\Text::Tabbify(...));

		////////

		file_put_contents($Filename, $Data->Get());

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Meta\DateAdded('2023-07-10')]
	#[Meta\Info('Load a datastore from a file on disk.')]
	static public function
	FromFile(string $Filename):
	static {

		$Store = new static;
		$Store->Read($Filename);
		$Store->SetFilename($Filename);

		return $Store;
	}

	#[Meta\DateAdded('2023-07-10')]
	#[Meta\Info('Load a datastore from a JSON string.')]
	static public function
	FromJSON(?string $JSON):
	static {

		$JSON ??= '[]';
		$Data = json_decode($JSON, TRUE);

		if(!is_array($Data))
		$Data = [];

		return static::FromArray($Data);
	}

	#[Meta\Date('2023-07-26')]
	#[Meta\Info('Load a datastore from an array.')]
	static public function
	FromArray(iterable $Input):
	static {

		$Store = new static($Input);

		return $Store;
	}

	#[Meta\DateAdded('2023-07-10')]
	#[Meta\Info('Perform a BlendRight upon all the arguments and return the final result.')]
	static public function
	FromStackBlended(iterable $OG, ...$Sets):
	static {

		$Store = new static($OG);
		$More = NULL;

		foreach($Sets as $More)
		if($More !== NULL)
		$Store->BlendRight($More);

		return $Store;
	}

	#[Meta\DateAdded('2023-07-10')]
	#[Meta\Info('Perform a MergeRight upon all the arguments and return the final result.')]
	static public function
	FromStackMerged(iterable $OG, ...$Updates):
	static {

		$Store = new static($OG);
		$More = NULL;

		foreach($Updates as $More)
		if($More !== NULL)
		$Store->MergeRight($More);

		return $Store;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	/**
	 * @codeCoverageIgnore
	 */

	#[Meta\Deprecated('2023-07-10', 'use FromStackBlended instead')]
	static public function
	NewBlended(iterable $OG, ...$Adds):
	static {

		return static::FromStackBlended($OG, ...$Adds);
	}

	/**
	 * @codeCoverageIgnore
	 */

	#[Meta\Deprecated('2023-07-10', 'use FromStackMerged instead')]
	static public function
	NewMerged(iterable $OG, ...$Updates):
	static {

		return static::FromStackMerged($OG, ...$Updates);
	}

	/**
	 * @codeCoverageIgnore
	 */

	#[Meta\DateAdded('2022-08-15')]
	#[Meta\Deprecated('2023-07-10', 'use FromFile instead')]
	static public function
	NewFromFile(string $Filename):
	static {

		return static::FromFile($Filename);
	}

	/**
	 * @codeCoverageIgnore
	 */

	#[Meta\DateAdded('2022-08-15')]
	#[Meta\Deprecated('2023-07-10', 'use FromJSON instead')]
	static public function
	NewFromJSON(?string $JSON):
	static {

		return static::FromJSON($JSON);
	}

}
