<?php ##########################################################################
################################################################################

namespace Nether\Common\Units;

use Nether\Common;

################################################################################
################################################################################

class HostPort {

	public string
	$Host;

	public int
	$Port;

	public mixed
	$Socket;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(string $Host, int $Port) {

		($this)
		->SetHost($Host)
		->SetPort($Port);

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	SetHost(string $Host):
	static {

		$this->Host = $Host;

		return $this;
	}

	public function
	SetPort(int $Port):
	static {

		$this->Port = $Port;

		return $this;
	}

	public function
	SetSocket(mixed $Resource):
	static {

		if(!is_resource($Resource))
		throw new Common\Error\FormatInvalid('PHP Resource');

		////////

		$this->Socket = $Resource;

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	GetHost():
	string {

		return $this->Host;
	}

	public function
	GetPort():
	int {

		return $this->Port;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	FromString(string $Input):
	static {

		$Output = NULL;
		$Host = NULL;
		$Port = NULL;

		////////

		if(!str_contains($Input, ':'))
		throw new Common\Error\FormatInvalid('host:port');

		////////

		list($Host, $Port) = explode(':', $Input, 2);

		$Output = new static($Host, $Port);

		return $Output;
	}

	static public function
	FromStringDefaultPort(string $Input, int $DefaultPort):
	static {

		if(!str_contains($Input, ':'))
		$Input = "{$Input}:{$DefaultPort}";

		return static::FromString($Input);
	}

};
