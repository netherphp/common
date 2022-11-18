<?php

namespace Nether\Common;

class PasswordTester {

	protected int
	$MinLength = 12;

	protected bool
	$RequireAlphaLower = TRUE;

	protected bool
	$RequireAlphaUpper = TRUE;

	protected bool
	$RequireNumeric = TRUE;

	protected string
	$Input;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(string $Input) {
		$this->Input = $Input;
		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	IsOK():
	bool {

		if(strlen($this->Input) < $this->MinLength)
		return FALSE;

		if($this->RequireAlphaLower)
		if(!preg_match('/[a-z]/', $this->Input))
		return FALSE;

		if($this->RequireAlphaUpper)
		if(!preg_match('/[A-Z]/', $this->Input))
		return FALSE;

		if($this->RequireNumeric)
		if(!preg_match('/[0-9]/', $this->Input))
		return FALSE;

		return TRUE;
	}

	public function
	GetDescription():
	string {

		$Types = [];
		$TypeStr = '';

		////////

		if($this->RequireAlphaLower)
		$Types[] = 'one lowercase letter (a-z)';

		if($this->RequireAlphaUpper)
		$Types[] = 'one uppercase letter (A-Z)';

		if($this->RequireNumeric)
		$Types[] = 'one number (0-9)';

		////////

		if(count($Types) > 1) {
			$TypeStr = join(', ', array_slice($Types, 0, -1));
			$TypeStr .= ' and ';
		}

		$TypeStr .= current(array_slice($Types, -1, 1));

		////////

		$Output = sprintf(
			'Must contain at least %d characters with %s.',
			$this->MinLength,
			$TypeStr
		);

		return $Output;
	}

	public function
	Get():
	string {

		return $this->Input;
	}

}
