<?php ##########################################################################
################################################################################

namespace Nether\Common\Units;

use Nether\Common\Filters;
use Nether\Common\Error;

use Stringable;

################################################################################
################################################################################

class EmailAddress
implements Stringable {

	protected string
	$Local;

	protected string
	$Domain;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__construct(string $EmailAddress) {

		$this->Set($EmailAddress);

		return;
	}

	public function
	__toString():
	string {

		return $this->Get();
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Get():
	string {

		return sprintf(
			'%s@%s',
			$this->Local,
			$this->Domain
		);
	}

	public function
	Set(string $EmailAddress):
	static {

		$EmailAddress = Filters\Text::Email($EmailAddress);

		if(!$EmailAddress)
		throw new Error\FormatInvalid('email address');

		////////

		list($this->Local, $this->Domain)
		= explode('@', $EmailAddress, 2);

		////////

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	GetLocal():
	string {

		return $this->Local;
	}

	public function
	GetDomain():
	string {

		return $this->Domain;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	FromEmail(string $EmailAddress):
	static {

		return new static($EmailAddress);
	}

};
