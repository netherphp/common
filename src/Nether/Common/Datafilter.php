<?php

namespace Nether\Common;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Exception;

use Nether\Common\Struct\DatafilterCallable;

class Datafilter
implements ArrayAccess, Countable, IteratorAggregate {

	protected bool
	$__Case = FALSE;

	protected array
	$__Filters = [];

	protected ?array
	$__Data = NULL;

	protected ?array
	$__Cache = NULL;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(object|array $Data, bool $Cache=TRUE, bool $Case=FALSE) {

		$this
		->SetCaseSensitive($Case)
		->SetCacheOutput($Cache)
		->SetData($Data);

		return;
	}

	public function
	__Get(string $Key):
	mixed {

		return $this->Get($Key);
	}

	public function
	__Set(string $Key, mixed $Value):
	void {

		$this->Set($Key, $Value);
		return;
	}

	public function
	__Call(string $Key, array $Argv):
	static {

		// $Filter->Key([ ... ]);
		if(isset($Argv[0]) && is_array($Argv[0])) {
			foreach($Argv[0] as $Input) {

				// $Filter->Key([ callable, ... ]);
				if(is_callable($Input))
				$this->AddFilter($Key, $Input);

				// $Filter->Key([ [ callable, args ], ... ]);
				if(is_array($Input))
				$this->AddFilter($Key, array_shift($Input), ...$Input);

			}

			return $this;
		}

		// $Filter->Key(callable, ...args);
		$this->AddFilter($Key, ...$Argv);

		return $this;
	}

	public function
	__DebugInfo():
	array {

		$Output = [
			'Filters' => $this->__Filters,
			'Data'    => $this->__Data
		];

		return $Output;
	}

	////////////////////////////////////////////////////////////////
	// implements ArrayAccess //////////////////////////////////////

	public function
	OffsetGet(mixed $Key):
	mixed {

		return $this->Get($Key);
	}

	public function
	OffsetSet(mixed $Key, mixed $Value):
	void {

		$this->{$Key} = $Value;
		return;
	}

	public function
	OffsetExists(mixed $Key):
	bool {

		$Key = $this->PrepareKey($Key);

		return array_key_exists($Key, $this->__Data);
	}

	public function
	OffsetUnset(mixed $Key):
	void {

		$Key = $this->PrepareKey((string)$Key);

		unset($this->__Data[$Key]);

		////////

		if(isset($this->__Cache))
		unset($this->__Cache[$Key]);

		/////////

		return;
	}

	////////////////////////////////////////////////////////////////
	// implements Countable ////////////////////////////////////////

	public function
	Count():
	int {

		return count($this->__Data);
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
				array_keys($this->__Data),
				array_keys($this->__Data)
			)
		));
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	CacheClear():
	static {

		if(isset($this->__Cache)) {
			$this->__Cache = NULL;
			$this->__Cache = [];
		}

		return $this;
	}

	public function
	CacheHas(string $Key):
	bool {

		$Key = $this->PrepareKey($Key);

		return (
			TRUE
			&& isset($this->__Cache)
			&& array_key_exists($Key, $this->__Cache)
		);
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Get(string $Key):
	mixed {

		$Key = $this->PrepareKey($Key);
		$Output = NULL;
		$Func = NULL;

		////////

		if(isset($this->__Cache) && array_key_exists($Key, $this->__Cache))
		return $this->__Cache[$Key];

		////////

		$Output = (
			(array_key_exists($Key, $this->__Data))
			? $Output = $this->__Data[$Key]
			: NULL
		);

		if(array_key_exists($Key, $this->__Filters))
		foreach($this->__Filters[$Key] as $Func) {
			if(is_callable($Func))
			$Output = ($Func)($Output, $Key, $this);
		}

		if(isset($this->__Cache))
		$this->__Cache[$Key] = $Output;

		return $Output;

		////////

		return NULL;
	}

	public function
	GetQueryString(?array $Overwrite=NULL):
	string {

		$Data = new Datastore($this->__Data);
		$Output = [];

		////////

		if($Overwrite !== NULL)
		$Data->MergeRight($Overwrite);

		$Data->Each(function($Val, $Key, $Self) {

			$Self[$Key] = sprintf(
				'%s=%s',
				urlencode($Key),
				urlencode($Val)
			);

			return;
		});

		return $Data->Join('&');
	}

	public function
	Exists(mixed $Key):
	bool {

		$Key = $this->PrepareKey($Key);

		return array_key_exists($Key, $this->__Data);
	}

	public function
	Set(string $Key, mixed $Value):
	static {

		$Key = $this->PrepareKey($Key);

		////////

		$this->__Data[$Key] = $Value;

		////////

		if(isset($this->__Cache))
		if(array_key_exists($Key, $this->__Cache))
		unset($this->__Cache[$Key]);

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	SetCaseSensitive(bool $Use):
	static {

		$this->__Case = $Use;

		if(isset($this->__Data) && !$Use)
		$this->SetData($this->__Data);

		return $this;
	}

	public function
	SetCacheOutput(bool $Use):
	static {

		if($Use) {
			if(!isset($this->__Cache))
			$this->__Cache = [];
		}

		else {
			$this->__Cache = NULL;
		}

		return $this;
	}

	public function
	SetData(array|object $Data):
	static {

		if($this->__Case) {
			$this->__Data = (array)$Data;
			$this->CacheClear();
			return $this;
		}

		////////

		$New = [];
		$Key = NULL;
		$Val = NULL;

		foreach($Data as $Key => $Val)
		$New[strtolower($Key)] = $Val;

		$this->__Data = $New;
		$this->CacheClear();

		////////

		return $this;
	}

	public function
	SetFilter(string $Key, callable|string|array $Func, ...$Argv):
	static {

		$this->ResetFilters($Key);
		$this->AddFilter($Key, $Func, ...$Argv);

		return $this;
	}

	public function
	AddFilter(string $Key, callable|string $Func, ...$Argv):
	static {

		$Key = $this->PrepareKey($Key);

		if(!is_callable($Func))
		throw new Exception('supplied filter not callable.');

		////////

		if(!array_key_exists($Key, $this->__Filters))
		$this->__Filters[$Key] = [];

		$this->__Filters[$Key][] = new DatafilterCallable($Func, $Argv);

		return $this;
	}

	public function
	ResetFilters(string $Key):
	static {

		$Key = $this->PrepareKey($Key);

		if(isset($this->__Filters[$Key]))
		unset($this->__Filters[$Key]);

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	PrepareKey(string $Key):
	string {

		if($this->__Case)
		return $Key;

		return strtolower($Key);
	}

}
