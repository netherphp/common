<?php

namespace Nether\Browser;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Nether\Common;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

#[Common\Meta\Date('2023-10-04')]
class Client
extends Common\Prototype {

	const
	ViaFileGetContents = 1,
	ViaCURL            = 2;

	////////

	public string
	$Method = 'GET';

	public string
	$URL;

	public string
	$UA;

	public int
	$Via = self::ViaFileGetContents;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected function
	OnReady(Common\Prototype\ConstructArgs $Args):
	void {

		$this->PrepareUA();

		return;
	}

	protected function
	PrepareUA():
	void {

		if(isset($this->UA))
		return;

		////////

		$UA = Library::Get(Key::ConfBrowserUA);

		if(!$UA)
		throw new Common\Error\RequiredDataMissing(
			Common\Key::ConfBrowserUA, 'config'
		);

		////////

		$this->UA = $UA;

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Common\Meta\Info('Generate a Stream Context for browser as configured.')]
	public function
	GenerateStreamContext():
	mixed {

		$CTX = stream_context_create([
			'http'  => [ 'method' => $this->Method, 'user_agent' => $this->UA ],
			'https' => [ 'method' => $this->Method, 'user_agent' => $this->UA ]
		]);

		return $CTX;
	}

	#[Common\Meta\Info('Fetch and return the data from the remote.')]
	public function
	Fetch():
	?string {

		$Output = match($this->Via) {
			static::ViaCURL
			=> $this->GetViaCURL(),

			static::ViaFileGetContents
			=> $this->GetViaFileGetContents(),

			default
			=> NULL
		};

		return $Output;
	}

	#[Common\Meta\Info('Fetch and digest data from the remote as JSON.')]
	public function
	FetchJSON():
	?array {

		$JSON = $this->Fetch();

		if(!$JSON)
		return NULL;

		$Data = json_decode($JSON, TRUE);

		if(!is_array($Data))
		return NULL;

		return $Data;
	}

	#[Common\Meta\Info('Fetch via cURL.')]
	public function
	GetViaCURL():
	?string {

		return NULL;
	}

	#[Common\Meta\Info('Fetch via native file_get_contents.')]
	public function
	GetViaFileGetContents():
	?string {

		$CTX = $this->GenerateStreamContext();
		$Data = file_get_contents($this->URL, FALSE, $CTX);

		return $Data;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	SetMethod(string $Method):
	static {

		$this->Method = $Method;

		return $this;
	}

	public function
	SetURL(string $URL):
	static {

		$this->URL = $URL;

		return $this;
	}

	public function
	SetUserAgent(string $UA):
	static {

		$this->UA = $UA;

		return $this;
	}

	public function
	SetVia(int $Via):
	static {

		$this->Via = $Via;

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	FromURL(string $URL):
	static {

		return new static([
			'URL' => $URL
		]);
	}

}
