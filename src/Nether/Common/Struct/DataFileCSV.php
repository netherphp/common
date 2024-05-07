<?php

namespace Nether\Common\Struct;

class DataFileCSV {

	protected string
	$Filename;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected mixed
	$Handle = NULL;

	public ?array
	$Headers = NULL;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(string $Filename, bool $Headers=FALSE) {
		$this->Filename = $Filename;
		$this->Open();

		if($Headers)
		$this->ReadHeaderRow();

		return;
	}

	public function
	Open():
	static {

		if(file_exists($this->Filename) && is_readable($this->Filename))
		$this->Handle = fopen($this->Filename, 'r');

		return $this;
	}

	public function
	Close():
	static {

		if($this->Handle) {
			fclose($this->Handle);
			$this->Handle = NULL;
		}

		return $this;
	}

	public function
	ReadHeaderRow():
	static {

		$this->Headers = fgetcsv($this->Handle);

		return $this;
	}

	public function
	Next():
	?array {

		$Row = fgetcsv($this->Handle);
		$Output = NULL;

		if(!$Row)
		return NULL;

		if($this->Headers === NULL || !count($this->Headers))
		return $Row;

		////////

		$Key = NULL;
		$Val = NULL;

		foreach($Row as $Key => $Val)
		$Output[$this->Headers[$Key]] = $Val;

		return $Output;
	}

	public function
	Glomp():
	array {

		$Output = [];
		$Row = NULL;

		while($Row = $this->Next())
		$Output[] = $Row;

		return $Output;
	}

}
