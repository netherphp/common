<?php

namespace Nether\Common;

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

	public function
	DescribeForPublicAPI():
	object {

		return (object)$this->Data->GetData();
	}

	public function
	Filter(callable $Func):
	Datastore {

		return $this->Data->Distill($Func);
	}

	public function
	Get(string $Key):
	mixed {

		return $this->Data->Get($Key);
	}

	public function
	HasKey(string $Key):
	mixed {

		return $this->Data->HasKey($Key);
	}

	public function
	HasValue(string $Key):
	mixed {

		return $this->Data->HasKey($Key);
	}

	public function
	Keys():
	array {

		return $this->Data->Keys();
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	FromArray(iterable $Input):
	static {

		$Output = new static;
		$Output->Data->SetData($Input);

		return $Output;
	}

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
