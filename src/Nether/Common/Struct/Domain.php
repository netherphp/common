<?php

namespace Nether\Common\Struct;

use Nether\Common;

use Stringable;

#[Common\Meta\Date('2023-07-27')]
class Domain
extends Common\Prototype
implements Stringable {

	protected string
	$Input;

	protected int
	$Level;

	protected string
	$Delim = '.';

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(string $Domain, int $Level=2) {

		$this->SetInput($Domain);
		$this->SetLevel($Level);

		return;
	}

	public function
	__Invoke(?string $Domain=NULL):
	string {

		if($Domain !== NULL)
		$this->SetInput($Domain);

		return $this->Get();
	}

	public function
	__ToString():
	string {

		return $this->Get();
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Get():
	string {

		$Input = $this->Input;
		$Delim = $this->Delim;
		$Level = $this->Level;

		// no delimiter found then you just get what we have.

		if(!str_contains($Input, $Delim))
		return $Input;

		// otherwise undress the domain down to the number of delimiters
		// its configured for.

		$Bits = explode($Delim, $Input);
		$Bobs = [];
		$Bit = count($Bits);

		while(($Bit--) > 0 && ($Level--) > 0)
		$Bobs[] = $Bits[$Bit];

		return join($Delim, array_reverse($Bobs));
	}

	public function
	Set(string $Domain):
	static {

		$this->SetInput($Domain);
		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	GetDelim():
	string {

		return $this->Delim;
	}

	public function
	SetDelim(string $Delim):
	static {

		$this->Delim = $Delim;

		return $this;
	}

	public function
	GetInput():
	string {

		return $this->Input;
	}

	public function
	SetInput(string $Domain):
	static {

		$this->Input = $Domain;

		return $this;
	}

	public function
	GetLevel():
	int {

		return $this->Level;
	}

	public function
	SetLevel(int $Level):
	static {

		$this->Level = max(1, $Level);

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	FromDomain(string $Domain, int $Level=2):
	static {

		return new static($Domain, $Level);
	}

}
