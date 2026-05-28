<?php ##########################################################################
################################################################################

namespace Nether\Common;

use ArrayAccess;

################################################################################
################################################################################

// I WOULD LIKE THIS TO REPLACE DATAFILTER EVENTUALLY.

// it requires migrating routes to use the methods to access instead of the
// magic properties because i am done with that. but i want to keep the case
// insensitive features for now and came up with a more managable way to do it.

################################################################################
################################################################################

class Databox
implements ArrayAccess {

	protected bool
	$LazyCased;

	protected Datastore
	$LazyMap;

	protected Datastore
	$LocalFilters;

	protected Datastore
	$LocalData;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__construct(iterable $Input) {

		$this->LazyMap = new Datastore;
		$this->LocalFilters = new Datastore;
		$this->LocalData = new Datastore;
		$this->SetLazyCased(FALSE);

		////////

		$this->Import($Input);

		////////

		return;
	}

	public function
	__invoke(string $Key, callable|array $Funcs, ?array $Argv=NULL):
	static {

		return $this->SetFilters('Key', $Funcs, $Argv);
	}

	////////////////////////////////////////////////////////////////
	// implement ArrayAccess ///////////////////////////////////////

	public function
	offsetExists(mixed $Key):
	bool {

		return $this->Exists($Key);
	}

	public function
	offsetGet(mixed $Key):
	mixed {

		return $this->Get($Key);
	}

	public function
	offsetSet(mixed $Key, mixed $Val):
	void {

		$this->Set($Key, $Val);
		return;
	}

	public function
	offsetUnset(mixed $Key):
	void {

		$this->Unset($Key);
		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Exists(string|int $Key):
	bool {

		if($this->LocalData->HasKey($Key))
		return TRUE;

		////////

		if($this->LazyCased) {
			$Use = $this->LookupLazyMap($Key);

			if($Use && $this->LocalData->HasKey($Use))
			return TRUE;
		}

		////////

		return FALSE;
	}

	public function
	Get(string|int $Key):
	mixed {

		if($this->LocalData->HasKey($Key))
		return $this->RunFilters($Key, $this->LocalData->Get($Key));

		////////

		if($this->LazyCased) {
			$Use = $this->LookupLazyMap($Key);

			if($Use && $this->LocalData->HasKey($Use))
			return $this->RunFilters($Use, $this->LocalData->Get($Use));
		}

		////////

		return NULL;
	}

	public function
	Set(string|int $Key, mixed $Val):
	static {

		$this->LocalData->Set($Key, $Val);

		////////

		if($this->LazyCased)
		$this->PushToLazyMap($Key);

		return $this;
	}

	public function
	Unset(string|int $Key):
	static {

		$this->LocalData->Remove($Key);

		////////

		if($this->LazyMap)
		$this->DropFromLazyMap($Key);

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	GetFilters(string $Key):
	?Datastore {

		if($this->LocalFilters->HasKey($Key))
		return $this->LocalFilters->Get($Key);

		return NULL;
	}

	public function
	SetFilters(string $Key, callable|iterable $Funcs, ?array $Argv=NULL):
	static {

		if(is_callable($Funcs))
		$Funcs = [ [ $Funcs, $Argv ] ];

		////////

		if(!$this->LocalFilters->HasKey($Key))
		$this->LocalFilters[$Key] = new Datastore;
		else
		$this->LocalFilters[$Key]->Clear();

		foreach($Funcs as $Call) {
			if(!is_callable($Call[0]))
			continue;

			$this->LocalFilters[$Key]->Push($Call);
		}

		////////

		return $this;
	}

	public function
	RunFilters(string $Key, mixed $Input):
	mixed {

		if(!$this->LocalFilters->HasKey($Key))
		return $Input;

		////////

		$Output = $this->LocalFilters[$Key]->Accumulate($Input,
			fn(mixed $In, array $Call)
			=> call_user_func($Call[0], $In, ...($Call[1] ?: []))
		);

		return $Output;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	FilterSet(string $Key, callable $Func, ...$Argv):
	static { return $this->SetFilters($Key, $Func, $Argv); }

	public function
	FilterPush($Key, $Func, ...$Argv):
	static { return $this->SetFilters($Key, $Func, $Argv); }

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Import(iterable $Input):
	static {

		($this->LocalData)
		->Clear()
		->Import($Input);

		////////

		if($this->LazyCased)
		$this->RebuildLazyMap();
		else
		$this->FlushLazyMap();

		////////

		return $this;
	}

	public function
	Export():
	array {

		return $this->LocalData->Export();
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	IsLazyCased():
	bool {

		return $this->LazyCased;
	}

	public function
	SetLazyCased(bool $Lazy):
	static {

		$this->LazyCased = $Lazy;

		////////

		if($this->LazyCased)
		$this->RebuildLazyMap();
		else
		$this->FlushLazyMap();

		////////

		return $this;
	}

	protected function
	LookupLazyMap(string|int $Key):
	string|null {

		$Use = strtolower($Key);

		////////

		if($this->LazyMap->HasKey($Use))
		return $this->LazyMap->Get($Use);

		return NULL;
	}

	protected function
	PushToLazyMap(string|int $Key):
	static {

		$Use = strtolower($Key);

		////////

		$this->LazyMap->Set($Use, $Key);

		return $this;
	}

	protected function
	DropFromLazyMap(string|int $Key):
	static {

		$Use = strtolower($Key);

		////////

		$this->LazyMap->Remove($Use);

		return $this;
	}

	protected function
	RebuildLazyMap():
	static {

		$this->FlushLazyMap();

		////////

		($this->LocalData)
		->EachKeyValue(
			fn(string|int $K, mixed $V)
			=> $this->PushToLazyMap($K)
		);

		////////

		return $this;
	}

	protected function
	FlushLazyMap():
	static {

		$this->LazyMap->Clear();

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

};
