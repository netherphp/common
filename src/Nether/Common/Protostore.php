<?php

namespace Nether\Common;

#[Meta\Date('2023-08-10')]
class Protostore {

	protected Datastore
	$Data;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(iterable $Input=NULL) {

		$this->Data = new Datastore;

		if($Input !== NULL)
		$this->Data->SetData($Input);

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Meta\Date('2023-08-10')]
	#[Meta\Info('Mimic the version on the Atlantis Prototype class to self-describe itself.')]
	public function
	DescribeForPublicAPI():
	object {

		// this is mainly to mimic the atlantis prototype version that
		// the json apis use. this one specifically casts the array to
		// an object to make my empty sets look consistent.

		return (object)$this->Data->GetData();
	}

	#[Meta\Date('2023-08-12')]
	public function
	Count():
	int {

		return $this->Data->Count();
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
	FromJSON(?string $JSON):
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
