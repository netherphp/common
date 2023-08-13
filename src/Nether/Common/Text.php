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

}
