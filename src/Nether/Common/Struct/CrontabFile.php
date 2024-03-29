<?php

namespace Nether\Common\Struct;

use Nether\Common;

class CrontabFile
extends Common\Datastore {

	public function
	Write(?string $Filename = NULL):
	static {

		$this->Filename = (
			$Filename
			?: $this->Filename
			?: Common\Filesystem\Util::MkTempFile()
		);

		////////

		file_put_contents(
			$this->Filename,
			sprintf('%s%s', trim($this->Join(PHP_EOL)), PHP_EOL)
		);

		return $this;
	}

	public function
	Apply():
	static {

		if(PHP_OS_FAMILY === 'Windows')
		return $this;

		////////

		$Commit = (FALSE
			|| defined('UNIT_TEST_GO_BRRRT') === FALSE
			|| isset($_ENV['UNIT_TEST_HITS_HARD']) === TRUE
		);

		if($Commit)
		system(sprintf('crontab - < %s', $this->Filename));

		////////

		return $this;
	}

	public function
	Clean():
	static {

		$this->Filter(static::FilterCrontabLine(...));

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	FilterCrontabLine(mixed $Line):
	bool {

		$Line = Common\Filters\Text::Trimmed($Line);

		if(!$Line)
		return FALSE;

		if(str_starts_with($Line, '#'))
		return FALSE;

		return TRUE;
	}

	static public function
	FetchViaSystemUser():
	static {

		if(PHP_OS_FAMILY === 'Windows')
		return new static;

		$Output = new static(
			(new Common\Datastore(explode("\n", `crontab -l`)))
			->Remap(fn(string $Line)=> trim($Line))
			->Remap(fn(string $Line)=> Common\Struct\CrontabEntry::FromCrontab($Line))
		);

		return $Output;
	}

}
