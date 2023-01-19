<?php

namespace Nether\Common\Struct;

use Nether\Common\Datafilter;

class DatafilterItem {

	public mixed
	$Key;

	public mixed
	$Value;

	public Datafilter
	$Source;

	public function
	__Construct(mixed $Value, string $Key, Datafilter $Source) {

		$this->Key = $Key;
		$this->Value = $Value;
		$this->Source = $Source;

		return;
	}

	public function
	__Invoke():
	mixed {

		return $this->Value;
	}

}
