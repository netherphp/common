<?php

namespace Nether\Common;

class Overbuffer {

	protected string
	$Buffer;

	protected bool
	$Keep;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(bool $Keep=TRUE) {

		$this->Keep = $Keep;
		$this->Clear();

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Clear():
	static {

		$this->Buffer = '';
		return $this;
	}

	public function
	Exec(callable $Fn):
	mixed {

		$Out = NULL;
		$Buf = NULL;

		////////

		ob_start();
		$Out = $Fn();
		$Buf = ob_get_clean();

		////////

		if($this->Keep)
		$this->Buffer .= $Buf;
		else
		$this->Buffer = $Buf;

		////////

		return $Out;
	}

	public function
	Filter(callable|iterable $Funcs):
	mixed {

		$Buffer = $this->Buffer;
		$Func = NULL;

		if(!is_iterable($Funcs))
		$Funcs = [ $Funcs ];

		////////

		foreach($Funcs as $Func)
		if(is_callable($Func))
		$Buffer = $Func($Buffer);

		////////

		$this->Buffer = $Buffer;

		return $this;
	}

	public function
	Get():
	string {

		return $this->Buffer;
	}

	public function
	Length():
	int {

		return strlen($this->Buffer);
	}

}
