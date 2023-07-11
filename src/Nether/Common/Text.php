<?php

namespace Nether\Common;

use Stringable;

class Text
extends Prototype
implements Stringable {

	const
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

		return sprintf(
			"\e[%sm%s\e[0m",
			join(';', $Codes),
			$this->Text
		);
	}

	public function
	RenderAsHTML(string $Tag='span'):
	string {

		$Styles = [];

		////////

		if($this->Bold)
		$Styles[] = 'font-weight: bold;';

		if($this->Italic)
		$Styles[] = 'font-style: italic;';

		if($this->Underline)
		$Styles[] = 'text-decoration: underline';

		if(isset($this->Colour))
		$Styles[] = "color: {$this->Colour->GetHexRGB()}";

		////////

		return sprintf(
			'<%1$s style="%2$s">%3$s</%1$s>',
			$Tag,
			join('; ', $Styles),
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
	New(string $Text=NULL, int $Mode=self::ModeTerminal, Units\Colour $Colour=NULL, bool $Bold=FALSE, bool $Italic=FALSE, bool $Underline=FALSE):
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