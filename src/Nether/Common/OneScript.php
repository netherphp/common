<?php

namespace Nether\Common;

use FileEye;

use Exception;

class OneScript {

	public string
	$Root;

	public ?string
	$Type;

	public ?string
	$Ext;

	public Datastore
	$Files;

	public Datastore
	$Headers;

	protected string
	$Output;

	protected
	$Outfile;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(string $Root, ?string $Outfile=NULL) {

		$this->Root = $Root;
		$this->Outfile = $Outfile;
		$this->Files = new Datastore;
		$this->Headers = new Datastore;
		$FileEye = NULL;

		if(!str_contains($Outfile, '.'))
		throw new Exception('cant tell what kind of file');

		////////

		$this->Ext = explode('.', $Outfile, 2)[1];

		$FileEye = new FileEye\MimeMap\Extension($this->Ext);
		$this->Type = $FileEye->GetDefaultType();

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	AddDir(string $Path):
	static {

		$Find = new Filesystem\Indexer($Path);
		$Files = new Datastore;
		$File = NULL;

		foreach($Find as $File) {
			if($this->Ext !== NULL)
			if(!str_ends_with($File->GetBasename(), ".{$this->Ext}"))
			continue;

			$Files->Push($File->GetPathname());
		}

		$Files->Sort(function($A, $B) {
			return $A <=> $B;
		});

		$this->Files->MergeRight($Files);

		return $this;
	}

	public function
	AddFile(string $Path):
	static {

		$this->Files->Push($Path);

		return $this;
	}

	public function
	GetFileTime($Path):
	int {

		$Filepath = Filesystem\Util::Pathify(
			$this->Root,
			$Path
		);

		return filemtime($Path) ?: 0;
	}

	public function
	GetFileSize($Path):
	int {

		$Filepath = Filesystem\Util::Pathify(
			$this->Root,
			$Path
		);

		return filesize($Path) ?: 0;
	}

	public function
	GetOutput():
	string {

		return $this->Output;
	}

	public function
	TransmitHeaders():
	static {

		$When = $this->GetFileTime($this->Outfile);

		header("content-type: {$this->Type}");

		return $this;
	}

	public function
	Render(bool $Force=FALSE):
	static {

		$Time = 0;
		$Update = FALSE;
		$File = NULL;

		$this->Output = '';
		$this->Files->Revalue();

		// determine if anything has changed such that it is even worth
		// rebuilding. if not then just bail with the current contents.

		if($this->Outfile && file_exists($this->Outfile)) {
			$Time = $this->GetFileTime($this->Outfile);

			$Update = $this->Files->Accumulate(
				FALSE,
				fn(bool $Prev, string $File)=> (
					FALSE
					|| ($Force || $Prev)
					|| ($this->GetFileTime($File) > $Time)
				)
			);

			if(!$Update) {
				$this->Output = file_get_contents($this->Outfile);
				return $this;
			}
		}

		// otherwise we want to compile all of the various scripts into a
		// single file with the most recent versions.

		$this->Output .= $this->RenderHeader();

		foreach($this->Files as $File)
		$this->Output .= $this->RenderFile($File);

		if($this->Outfile)
		file_put_contents($this->Outfile, $this->Output);

		////////

		return $this;
	}

	public function
	Print(bool $Headers=FALSE, bool $Force=FALSE):
	static {

		if(!isset($this->Output))
		$this->Render($Force);

		if($Headers)
		$this->TransmitHeaders();

		////////

		echo $this->Output;

		////////

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	RenderHeader():
	string {

		return match($this->Type) {
			'text/css'
			=> $this->RenderHeaderAsCSS(),

			default
			=> $this->RenderHeaderAsText()
		};
	}

	protected function
	RenderHeaderAsCSS():
	string {

		$Now = new Date;
		$Now->SetDateFormat(Values::DateFormatYMDT24VZ);

		$Output  = "/*//\n";
		$Output .= sprintf("@date %s\n", (string)$Now);

		$Output .= "@files {$this->Files->Count()} ";
		$Output .= json_encode($this->Files, JSON_PRETTY_PRINT);
		$Output .= "\n";

		$Output .= "//*/\n\n";

		return $Output;
	}

	protected function
	RenderHeaderAsText():
	string {

		$Now = new Date;
		$Now->SetDateFormat(Values::DateFormatYMDT24VZ);
		$Output = '';

		$Output .= sprintf("# @date %s\n", (string)$Now);

		$Output .= "# @files {$this->Files->Count()} ";
		$Output .= json_encode($this->Files, JSON_PRETTY_PRINT);
		$Output .= "\n\n";

		return $Output;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	RenderFile(string $Path):
	string {

		return match($this->Type) {
			'text/css'
			=> $this->RenderFileAsCSS($Path),

			default
			=> $this->RenderFileAsText($Path)
		};
	}

	protected function
	RenderFileAsCSS(string $Path):
	string {

		$Filepath = Filesystem\Util::Pathify($this->Root, $Path);

		$Output  = sprintf("/*//%s\n", str_repeat('/', 76));
		$Output .= sprintf("//// %s %s*/\n\n", $Path, str_repeat('/', (72 - strlen($Path))));

		$Output .= trim(file_get_contents($Filepath));
		$Output .= "\n\n";

		return $Output;
	}

	protected function
	RenderFileAsText(string $Path):
	string {

		$Filepath = Filesystem\Util::Pathify($this->Root, $Path);
		$Output = '';

		$Output .= sprintf("## %s\n\n", $Path);
		$Output .= trim(file_get_contents($Filepath));
		$Output .= "\n\n";

		return $Output;
	}

}