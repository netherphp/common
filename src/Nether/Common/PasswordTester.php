<?php

namespace Nether\Common;

class PasswordTester {

	protected int
	$MinLength = 10;

	protected bool
	$RequireAlphaLower = TRUE;

	protected bool
	$RequireAlphaUpper = TRUE;

	protected bool
	$RequireNumeric = TRUE;

	protected bool
	$RequireSpecial = TRUE;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct() {

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	SetMinLength(int $Len):
	static {

		$this->MinLength = $Len;
		return $this;
	}

	public function
	SetRequireAlphaLower(bool $Req):
	static {

		$this->RequireAlphaLower = $Req;
		return $this;
	}

	public function
	SetRequireAlphaUpper(bool $Req):
	static {

		$this->RequireAlphaUpper = $Req;
		return $this;
	}

	public function
	SetRequireNumeric(bool $Req):
	static {

		$this->RequireNumeric = $Req;
		return $this;
	}

	public function
	SetRequireSpecial(bool $Req):
	static {

		$this->RequireSpecial = $Req;
		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	IsOK(string $Input):
	bool {

		if(strlen($Input) < $this->MinLength)
		return FALSE;

		if($this->RequireAlphaLower)
		if(!preg_match('/[a-z]/', $Input))
		return FALSE;

		if($this->RequireAlphaUpper)
		if(!preg_match('/[A-Z]/', $Input))
		return FALSE;

		if($this->RequireNumeric)
		if(!preg_match('/[0-9]/', $Input))
		return FALSE;

		if($this->RequireSpecial)
		if(!preg_match('/[^a-zA-Z0-9]/', $Input))
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
		$Types[] = 'a lowercase letter (a-z)';

		if($this->RequireAlphaUpper)
		$Types[] = 'an uppercase letter (A-Z)';

		if($this->RequireNumeric)
		$Types[] = 'a number (0-9)';

		if($this->RequireSpecial)
		$Types[] = 'a special character';

		////////

		if(count($Types) > 1) {
			$TypeStr = join(', ', array_slice($Types, 0, -1));
			$TypeStr .= ', and ';
		}

		$TypeStr .= current(array_slice($Types, -1, 1));

		////////

		$Output = sprintf(
			'Must contain at least %d characters%s%s.',
			$this->MinLength,
			(count($Types) ? ' with ' : ''),
			$TypeStr
		);

		return $Output;
	}

}
