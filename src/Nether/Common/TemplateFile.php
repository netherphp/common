<?php

namespace Nether\Common;

class TemplateFile {

	protected string
	$Filename;

	protected bool
	$Cache;

	protected string
	$Data;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(string $Filename, bool $Autoload=TRUE, bool $Cache=FALSE) {

		$this->SetFilename($Filename);
		$this->SetCache($Cache);

		if($Autoload)
		$this->Load();

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Load(string $Filename=NULL):
	static {

		$Filename ??= $this->Filename;

		////////

		if(!file_exists($Filename))
		throw new Error\FileNotFound($Filename);

		if(!is_readable($Filename))
		throw new Error\FileUnreadable($Filename);

		////////

		$this->Data = match(TRUE) {
			($this->Cache && TemplateCache::Has($Filename))
			=> TemplateCache::Get($Filename),

			default
			=> file_get_contents($Filename)
		};

		if($this->Cache)
		TemplateCache::Set($Filename, $this->Data);

		////////

		return $this;
	}

	public function
	FindTokens():
	Datastore {

		return Text::TemplateFindTokens($this->Data);
	}

	public function
	ReplaceTokensWith(iterable $Tokens):
	string {

		$Output = $this->Data;
		$Old = NULL;
		$New = NULL;

		////////

		foreach($Tokens as $Old => $New) {
			$Output = str_replace(
				Text::TemplateMakeToken($Old),
				$New,
				$Output
			);
		}

		return $Output;
	}

	public function
	UpdateTokensWith(iterable $Tokens):
	static {

		$this->Data = $this->ReplaceTokensWith($Tokens);

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	GetCache():
	bool {

		return $this->Cache;
	}

	public function
	SetCache(bool $Use):
	static {

		$this->Cache = $Use;
		return $this;
	}

	public function
	GetFilename():
	string {

		return $this->Filename;
	}

	public function
	SetFilename(string $Filename):
	static {

		$this->Filename = $Filename;
		return $this;
	}

	public function
	GetData():
	?string {

		if(!isset($this->Data))
		return NULL;

		return $this->Data;
	}

	public function
	SetData(?string $Input):
	static {

		if($Input === NULL) {
			unset($this->Data);
			return $this;
		}

		$this->Data = $Input;
		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	FromFile(string $Filename):
	static {

		return new static($Filename, TRUE);
	}

};
