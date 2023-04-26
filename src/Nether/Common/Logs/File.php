<?php

namespace Nether\Common\Logs;

use Monolog;

class File {

	const
	Info = Monolog\Level::Info,
	Error = Monolog\Level::Error;

	protected string
	$Filename;

	////////

	protected Monolog\Logger
	$MLog;

	protected Monolog\Handler\StreamHandler
	$MStream;

	protected Monolog\Handler\BufferHandler
	$MBuffer;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(string $Name, string $Filename) {

		$this->Filename = static::RewritePathTokens($Filename);
		$this->MLog = new Monolog\Logger($Name);

		////////

		$this->MStream = new Monolog\Handler\StreamHandler(
			$this->Filename,
			Monolog\Level::Debug
		);

		$this->MBuffer = new Monolog\Handler\BufferHandler(
			$this->MStream
		);

		$this->MLog->PushHandler($this->MBuffer);

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	GetFilename():
	string {

		return $this->Filename;
	}

	public function
	Flush():
	static {

		$this->MBuffer->Flush();

		return $this;
	}

	public function
	Write(string $Message, array $Context=[], Monolog\Level $Level=self::Info):
	static {

		if($this->MLog instanceof Monolog\Logger)
		$this->MLog->Log($Level, $Message, $Context);

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	RewritePathTokens(string $Input):
	string {

		$Output = $Input;
		$Date = explode('-', date('Y-m-d-H-i-s'));
		$Tok = NULL;
		$Val = NULL;

		$Tokens = [
			'{Y}' => $Date[0],
			'{M}' => $Date[1],
			'{D}' => $Date[2],
			'{H}' => $Date[3],
			'{I}' => $Date[4],
			'{S}' => $Date[5]
		];

		foreach($Tokens as $Tok => $Val)
		$Output = str_replace($Tok, $Val, $Output);

		return $Output;
	}

}
