<?php

namespace Nether\Common\Struct;

use Nether\Common;

class CrontabFile
extends Common\Datastore {

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	CleanCrontabLine(mixed $Line):
	bool {

		$Line = trim(Common\Datafilters::Prepare($Line));

		if(!$Line)
		return FALSE;

		if(str_starts_with($Line, '#'))
		return FALSE;

		return TRUE;
	}

	static public function
	FetchViaSystemUser():
	static {

		$Output = new static(
			(new Common\Datastore(explode("\n", `crontab -l`)))
			->Remap(fn(string $Line)=> trim($Line))
			->Remap(fn(string $Line)=> Common\Struct\CrontabEntry::FromCrontab($Line))
		);

		return $Output;
	}

}
