<?php

namespace Nether\Common;

use ArrayAccess;
use Countable;
use Iterator;
use ReturnTypeWillChange;

#[Meta\Date('2023-08-10')]
class Protostore
implements
	ArrayAccess,
	Iterator,
	Countable {

	protected Datastore
	$Data;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(iterable $Input=NULL) {

		$this->Data = new Datastore;

		if($Input !== NULL)
		$this->Data->Import($Input);

		return;
	}


	////////////////////////////////////////////////////////////////
	// IMPLEMENTS Countable ////////////////////////////////////////

	#[Meta\Date('2024-06-14')]
	public function
	Count():
	int {

		return $this->Data->Count();
	}

	////////////////////////////////////////////////////////////////
	// IMPLEMENTS ArrayAccess //////////////////////////////////////

	#[Meta\Date('2023-09-14')]
	public function
	OffsetExists(mixed $Key):
	bool {

		return $this->Data->OffsetExists($Key);
	}

	#[Meta\Date('2023-09-14')]
	public function
	OffsetGet(mixed $Key):
	mixed {

		return $this->Data->OffsetGet($Key);
	}

	#[Meta\Date('2023-09-14')]
	public function
	OffsetSet(mixed $Key, mixed $Value):
	void {

		$this->Data->OffsetSet($Key, $Value);

		return;
	}

	#[Meta\Date('2023-09-14')]
	public function
	OffsetUnset($Key):
	void {

		$this->Data->OffsetUnset($Key);

		return;
	}

	////////////////////////////////////////////////////////////////
	// IMPLEMENTS Iterator /////////////////////////////////////////

	#[Meta\Date('2023-09-14')]
	public function
	Current():
	mixed {

		return $this->Data->Current();
	}

	#[Meta\Date('2023-09-14')]
	public function
	Key():
	int|string {

		return $this->Data->Key();
	}

	#[Meta\Date('2023-09-14')]
	#[ReturnTypeWillChange]
	public function
	Next():
	void {

		$this->Data->Next();

		return;
	}

	#[Meta\Date('2023-09-14')]
	#[ReturnTypeWillChange]
	public function
	Rewind():
	void {

		$this->Data->Rewind();

		return;
	}

	#[Meta\Date('2023-09-14')]
	public function
	Valid():
	bool {

		return $this->Data->Valid();
	}

	////////////////////////////////////////////////////////////////
	// EMULATE: Atlantis\Prototype /////////////////////////////////

	#[Meta\Date('2023-08-10')]
	public function
	DescribeForPublicAPI():
	object {

		// this is mainly to mimic the atlantis prototype version that
		// the json apis use. this one specifically casts the array to
		// an object to make my empty sets look consistent.

		return (object)$this->Data->GetData();
	}

	////////////////////////////////////////////////////////////////
	// Local Dataset API ///////////////////////////////////////////

	#[Meta\Date('2024-06-14')]
	public function
	Import(iterable $Input):
	static {

		$this->Data->Import($Input);

		return $this;
	}

	#[Meta\Date('2023-08-10')]
	public function
	Export():
	array {

		return $this->Data->Export();
	}

	#[Meta\Date('2023-08-10')]
	public function
	Filter(callable $Func):
	Datastore {

		return $this->Data->Distill($Func);
	}

	#[Meta\Date('2023-08-10')]
	public function
	Get(string $Key):
	mixed {

		return $this->Data->Get($Key);
	}

	#[Meta\Date('2023-08-10')]
	#[Meta\Deprecated('2024-06-06', 'use Export() instead')]
	public function
	GetData():
	array {

		return $this->Data->GetData();
	}

	#[Meta\Date('2023-08-12')]
	public function
	GetDatastore():
	Datastore {

		return new Datastore($this->GetData());
	}

	#[Meta\Date('2023-08-10')]
	public function
	HasKey(string $Key):
	mixed {

		return $this->Data->HasKey($Key);
	}

	#[Meta\Date('2023-08-10')]
	public function
	HasValue(string $Key):
	mixed {

		return $this->Data->HasValue($Key);
	}

	#[Meta\Date('2023-08-10')]
	public function
	Keys():
	array {

		return $this->Data->Keys();
	}

	#[Meta\Date('2023-08-10')]
	public function
	Set(string $Key, mixed $Val):
	static {

		$this->Data[$Key] = $Val;

		return $this;
	}

	#[Meta\Date('2023-08-12')]
	public function
	Unset(string $Key):
	static {

		unset($this->Data[$Key]);

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Meta\Date('2023-08-10')]
	static public function
	FromArray(iterable $Input):
	static {

		$Output = new static;
		$Output->Data->SetData($Input);

		return $Output;
	}

	#[Meta\Date('2023-08-10')]
	static public function
	FromJSON(?string $JSON='{}'):
	static {

		if(!$JSON)
		$JSON = '{}';

		////////

		$Data = json_decode($JSON, TRUE);

		if(!is_array($Data))
		$Data = [];

		////////

		return static::FromArray($Data);
	}

}
