<?php

namespace Nether\Common\Struct;

use Nether\Common;

class CrontabFile
extends Common\Datastore {

	public function
	Write(?string $Filename = NULL):
	static {

		if($Filename !== NULL)
		return parent::Write($Filename);

		////////

		$Data = $this->Join(PHP_EOL);
		$TmpFile = Common\Filesystem\Util::MkTempFile();

		file_put_contents(
			$TmpFile,
			sprintf('%s%s', trim($Data), PHP_EOL)
		);

		system(sprintf('crontab - < %s', $TmpFile));
		unlink($TmpFile);

		return $this;
	}

	public function
	Clean():
	static {

		$this->Filter($this::FilterCrontabLine(...));

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	FilterCrontabLine(mixed $Line):
	bool {

		$Line = trim(Common\Filters\Text::Prepare($Line));

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
