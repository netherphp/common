<?php

namespace Nether\Common\Struct;

use Nether\Common\Datafilter;
use Nether\Common\Struct\DatafilterItem;

class DatafilterCallable {

	public mixed
	$Func;

	public ?array
	$Argv;

	public function
	__Construct(callable $Func, ?array $Argv=NULL) {

		$this->Func = $Func;
		$this->Argv = $Argv;

		return;
	}

	public function
	__Invoke(mixed $Val, string $Key, Datafilter $Input):
	mixed {

		return ($this->Func)(
			new DatafilterItem($Val, $Key, $Input),
			...($this->Argv ?? [])
		);
	}

	public function
	__DebugInfo():
	array {

		$Output = [];

		if(is_callable($this->Func)) {
			$Output['Func'] = gettype($this->Func);
			$Output['Argv'] = $this->Argv;
		}

		return $Output;
	}

}
