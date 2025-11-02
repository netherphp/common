<?php

namespace Nether\Common;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Stringable;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class Overbuffer
implements
	Stringable {

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

	public function
	__ToString():
	string {

		return $this->Get();
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Clear():
	static {

		$this->Buffer = '';
		return $this;
	}

	#[Meta\Date('2023-08-12')]
	public function
	Execute(callable $Fn):
	mixed {

		$Out = NULL;
		$Buf = NULL;

		////////

		$this->Start();
		$Out = $Fn();
		$Buf = $this->Stop();

		////////

		if($this->Keep)
		$this->Buffer .= $Buf;
		else
		$this->Buffer = $Buf;

		////////

		return $Out;
	}

	#[Meta\Date('2023-11-23')]
	public function
	Start():
	static {

		ob_start();

		return $this;
	}

	#[Meta\Date('2023-11-23')]
	public function
	Stop():
	static {

		$this->Buffer = ob_get_clean();

		return $this;
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

	#[Meta\Date('2023-08-13')]
	public function
	SetKeep(bool $Should):
	static {

		$this->Keep = $Should;

		return $this;
	}

	#[Meta\Date('2023-08-13')]
	public function
	GetKeep():
	bool {

		return $this->Keep;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	/**
	 * @codeCoverageIgnore
	 */

	#[Meta\Deprecated('2023-08-12', 'Use Execute')]
	public function
	Exec(callable $Fn):
	mixed {

		return $this->Execute($Fn);
	}

}
