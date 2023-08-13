<?php

namespace NetherTestSuite\Common;

use Nether\Common;

use Exception;
use PHPUnit\Framework\TestCase;

class TextTest
extends TestCase {

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	/** @test */
	public function
	TestTextPlain():
	void {

		$Data = [
			[
				'Text'      => 'Test',
				'Expect'    => 'Test'
			],
			[
				'Text'      => 'Test',
				'Bold'      => TRUE,
				'Expect'    => 'Test'
			],
			[
				'Text'      => 'Test',
				'Italic'    => TRUE,
				'Expect'    => 'Test'
			],
			[
				'Text'      => 'Test',
				'Underline' => TRUE,
				'Expect'    => 'Test'
			],
			[
				'Text'      => 'Test',
				'Bold'      => TRUE,
				'Underline' => TRUE,
				'Italic'    => TRUE,
				'Expect'    => 'Test'
			],
			[
				'Text'      => 'Test',
				'Colour'    => Common\Units\Colour::FromString('red'),
				'Expect'    => 'Test'
			],
			[
				'Text'      => 'Test',
				'Colour'    => Common\Units\Colour::FromString('red'),
				'Bold'      => TRUE,
				'Expect'    => 'Test'
			]
		];

		$Set = NULL;
		$Expect = NULL;
		$Text = NULL;

		foreach($Data as $Set) {
			$Expect = array_pop($Set);
			$Text = Common\Text::New(...$Set);

			$this->AssertEquals($Expect, $Text->Get());
			$this->AssertEquals($Expect, $Text->Render());
			$this->AssertEquals($Expect, (string)$Text);

			ob_start();
			$Text->Print();
			$this->AssertEquals($Expect, ob_get_clean());
		}



		return;
	}

	/** @test */
	public function
	TestTextTerminal():
	void {

		$Data = [
			[
				'Mode'      => Common\Text::ModeTerminal,
				'Text'      => 'Test',
				'Expect'    => 'Test'
			],
			[
				'Mode'      => Common\Text::ModeTerminal,
				'Text'      => 'Test',
				'Bold'      => TRUE,
				'Expect'    => "\e[1mTest\e[0m"
			],
			[
				'Mode'      => Common\Text::ModeTerminal,
				'Text'      => 'Test',
				'Italic'    => TRUE,
				'Expect'    => "\e[3mTest\e[0m"
			],
			[
				'Mode'      => Common\Text::ModeTerminal,
				'Text'      => 'Test',
				'Underline' => TRUE,
				'Expect'    => "\e[4mTest\e[0m"
			],
			[
				'Mode'      => Common\Text::ModeTerminal,
				'Text'      => 'Test',
				'Bold'      => TRUE,
				'Underline' => TRUE,
				'Italic'    => TRUE,
				'Expect'    => "\e[1;3;4mTest\e[0m"
			],
			[
				'Mode'      => Common\Text::ModeTerminal,
				'Text'      => 'Test',
				'Colour'    => Common\Units\Colour::FromString('red'),
				'Expect'    => "\e[38;2;255;0;0mTest\e[0m"
			],
			[
				'Mode'      => Common\Text::ModeTerminal,
				'Text'      => 'Test',
				'Colour'    => Common\Units\Colour::FromString('red'),
				'Bold'      => TRUE,
				'Expect'    => "\e[1;38;2;255;0;0mTest\e[0m"
			]
		];

		$Set = NULL;
		$Expect = NULL;
		$Text = NULL;

		foreach($Data as $Set) {
			$Expect = array_pop($Set);
			$Text = Common\Text::New(...$Set);

			$this->AssertEquals($Expect, $Text->Get());
		}

		return;
	}

	/** @test */
	public function
	TestTextDivHTML():
	void {

		$Data = [
			[
				'Mode'      => Common\Text::ModeTagDiv,
				'Text'      => 'Test',
				'Expect'    => '<div>Test</div>'
			],
			[
				'Mode'      => Common\Text::ModeTagDiv,
				'Text'      => 'Test',
				'Bold'      => TRUE,
				'Expect'    => '<div style="font-weight: bold;">Test</div>'
			],
			[
				'Mode'      => Common\Text::ModeTagDiv,
				'Text'      => 'Test',
				'Italic'    => TRUE,
				'Expect'    => '<div style="font-style: italic;">Test</div>'
			],
			[
				'Mode'      => Common\Text::ModeTagDiv,
				'Text'      => 'Test',
				'Underline' => TRUE,
				'Expect'    => '<div style="text-decoration: underline;">Test</div>'
			],
			[
				'Mode'      => Common\Text::ModeTagDiv,
				'Text'      => 'Test',
				'Bold'      => TRUE,
				'Underline' => TRUE,
				'Italic'    => TRUE,
				'Expect'    => '<div style="font-weight: bold; font-style: italic; text-decoration: underline;">Test</div>'
			],
			[
				'Mode'      => Common\Text::ModeTagDiv,
				'Text'      => 'Test',
				'Colour'    => Common\Units\Colour::FromString('red'),
				'Expect'    => '<div style="color: #ff0000;">Test</div>'
			],
			[
				'Mode'      => Common\Text::ModeTagDiv,
				'Text'      => 'Test',
				'Colour'    => Common\Units\Colour::FromString('red'),
				'Bold'      => TRUE,
				'Expect'    => '<div style="font-weight: bold; color: #ff0000;">Test</div>'
			]
		];

		$Set = NULL;
		$Expect = NULL;
		$Text = NULL;

		foreach($Data as $Set) {
			$Expect = array_pop($Set);
			$Text = Common\Text::New(...$Set);

			$this->AssertEquals($Expect, $Text->Get());
		}

		return;
	}

	/** @test */
	public function
	TestTextSpanHTML():
	void {

		$Data = [
			[
				'Mode'      => Common\Text::ModeTagSpan,
				'Text'      => 'Test',
				'Expect'    => '<span>Test</span>'
			],
			[
				'Mode'      => Common\Text::ModeTagSpan,
				'Text'      => 'Test',
				'Bold'      => TRUE,
				'Expect'    => '<span style="font-weight: bold;">Test</span>'
			],
			[
				'Mode'      => Common\Text::ModeTagSpan,
				'Text'      => 'Test',
				'Italic'    => TRUE,
				'Expect'    => '<span style="font-style: italic;">Test</span>'
			],
			[
				'Mode'      => Common\Text::ModeTagSpan,
				'Text'      => 'Test',
				'Underline' => TRUE,
				'Expect'    => '<span style="text-decoration: underline;">Test</span>'
			],
			[
				'Mode'      => Common\Text::ModeTagSpan,
				'Text'      => 'Test',
				'Bold'      => TRUE,
				'Underline' => TRUE,
				'Italic'    => TRUE,
				'Expect'    => '<span style="font-weight: bold; font-style: italic; text-decoration: underline;">Test</span>'
			],
			[
				'Mode'      => Common\Text::ModeTagSpan,
				'Text'      => 'Test',
				'Colour'    => Common\Units\Colour::FromString('red'),
				'Expect'    => '<span style="color: #ff0000;">Test</span>'
			],
			[
				'Mode'      => Common\Text::ModeTagSpan,
				'Text'      => 'Test',
				'Colour'    => Common\Units\Colour::FromString('red'),
				'Bold'      => TRUE,
				'Expect'    => '<span style="font-weight: bold; color: #ff0000;">Test</span>'
			]
		];

		$Set = NULL;
		$Expect = NULL;
		$Text = NULL;

		foreach($Data as $Set) {
			$Expect = array_pop($Set);
			$Text = Common\Text::New(...$Set);

			$this->AssertEquals($Expect, $Text->Get());
		}

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	/** @test */
	public function
	TestDumpVar():
	void {

		$Data = [ 'One'=> 1, 'Two'=> 2 ];
		$Crap = [];
		$Expect = [];

		$Crap[] = 'array(2) {';
		$Crap[] = '  ["One"]=>';
		$Crap[] = '  int(1)';
		$Crap[] = '  ["Two"]=>';
		$Crap[] = '  int(2)';
		$Crap[] = '}';
		$Crap = sprintf('%s%s', join(chr(10), $Crap), chr(10));

		$Expect[] = 'array(2) {';
		$Expect[] = '	["One"] => int(1)';
		$Expect[] = '	["Two"] => int(2)';
		$Expect[] = '}';
		$Expect = sprintf('%s%s', join(chr(10), $Expect), chr(10));

		// php's crap format.

		ob_start();
		var_dump($Data);
		$Dump = ob_get_clean();
		$this->AssertTrue(strlen($Dump) > 0);
		$this->AssertEquals($Crap, $Dump, 'PHP (probably Xdebug) altered var_dump?');

		// more readable format.

		ob_start();
		Common\Dump::Var($Data);
		$this->AssertEquals($Expect, ob_get_clean());

		ob_start();
		Common\Dump::Var($Data, TRUE);
		$this->AssertEquals("<pre>{$Expect}</pre>", ob_get_clean());

		return;
	}

}