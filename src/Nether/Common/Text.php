<?php

namespace Nether\Common;

use Stringable;

class Text
extends Prototype
implements Stringable {

	const
	ModePlain    = 0,
	ModeTerminal = 1,
	ModeTagSpan  = 2,
	ModeTagDiv   = 3;

	////////

	public int
	$Mode;

	public string
	$Text;

	////////

	public bool
	$Bold;

	public bool
	$Italic;

	public bool
	$Underline;

	public ?Units\Colour
	$Colour;

	////////////////////////////////////////////////////////////////
	// implements Stringable ///////////////////////////////////////

	public function
	__ToString():
	string {

		return $this->Render();
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Get(?int $Mode=NULL):
	string {

		return $this->Render($Mode);
	}

	public function
	Print(?int $Mode=NULL):
	static {

		echo $this->Render($Mode);
		return $this;
	}

	public function
	Render(?int $Mode=NULL):
	string {

		$Mode ??= $this->Mode;

		return match($Mode) {
			static::ModeTerminal
			=> $this->RenderAsTerm(),

			static::ModeTagSpan
			=> $this->RenderAsHTML('span'),

			static::ModeTagDiv
			=> $this->RenderAsHTML('div'),

			default
			=> $this->RenderAsPlain()
		};
	}

	public function
	RenderAsTerm():
	string {

		$Codes = [];

		////////

		if($this->Bold)
		$Codes[] = 1;

		if($this->Italic)
		$Codes[] = 3;

		if($this->Underline)
		$Codes[] = 4;

		if(isset($this->Colour))
		array_push($Codes, ...[
			38, 2,
			$this->Colour->R(),
			$this->Colour->G(),
			$this->Colour->B()
		]);

		////////

		if(count($Codes))
		return sprintf(
			"\e[%sm%s\e[0m",
			join(';', $Codes),
			$this->Text
		);

		return $this->Text;
	}

	public function
	RenderAsHTML(string $Tag='span'):
	string {

		$Styles = [];

		////////

		if($this->Bold)
		$Styles[] = 'font-weight: bold';

		if($this->Italic)
		$Styles[] = 'font-style: italic';

		if($this->Underline)
		$Styles[] = 'text-decoration: underline';

		if(isset($this->Colour))
		$Styles[] = "color: {$this->Colour->GetHexRGB()}";

		if(count($Styles))
		$Styles = sprintf(' style="%s;"', join('; ', $Styles));
		else
		$Styles = '';

		////////

		return sprintf(
			'<%1$s%2$s>%3$s</%1$s>',
			$Tag,
			$Styles,
			$this->Text
		);
	}

	public function
	RenderAsPlain():
	string {

		return $this->Text;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	New(string $Text=NULL, int $Mode=self::ModePlain, Units\Colour $Colour=NULL, bool $Bold=FALSE, bool $Italic=FALSE, bool $Underline=FALSE):
	static {

		return new static([
			'Text'      => ($Text ?? ''),
			'Mode'      => $Mode,
			'Colour'    => $Colour,
			'Bold'      => $Bold,
			'Italic'    => $Italic,
			'Underline' => $Underline
		]);
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	TemplateFindTokens(?string $Input):
	Datastore {

		$Match = NULL;

		preg_match_all(
			'#\\{%([A-Za-z0-9_-]+)%\\}#',
			$Input, $Match
		);

		return Datastore::FromArray($Match[1]);
	}

	static public function
	TemplateMakeToken(string $Input):
	string {

		return "{%{$Input}%}";
	}

	static public function
	TemplateReplaceTokens(string $Input, iterable $Tokens):
	string {

		$Output = $Input;
		$Token = NULL;
		$Value = NULL;

		foreach($Tokens as $Token => $Value)
		$Output = str_replace(
			static::TemplateMakeToken($Token),
			$Value,
			$Output
		);

		return $Output;
	}

	static public function
	ReadableJSON(mixed $Input):
	string {

		$Data = json_encode($Input, JSON_PRETTY_PRINT);

		if($Data === FALSE)
		throw new Error\RequiredDataMissing('Input', 'Something JSONable');

		////////

		return Filters\Text::Tabbify($Data);
	}

	static public function
	IndentWithTab(string $Input, int $Inc=1):
	string {

		$Output = sprintf("\t%s", str_replace("\n", "\n\t", $Input));

		return $Output;
	}

	static public function
	MarkdownToHTML(string $Input):
	string {

		$Parse = new \Parsedown;
		$Parse->SetSafeMode(TRUE);

		$Output = $Parse->Text($Input);

		return $Output;
	}

}
