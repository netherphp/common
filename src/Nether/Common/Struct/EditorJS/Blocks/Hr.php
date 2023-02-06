<?php

namespace Nether\Common\Struct\EditorJS\Blocks;

use Nether\Common;

class Hr
extends Common\Struct\EditorJS\Block {

	protected function
	OnReady(Common\Prototype\ConstructArgs $Argv):
	void {
		parent::OnReady($Argv);

		($this->Data)
		->Mode(Common\Datafilters::StrippedText(...));

		return;
	}

	public function
	__ToString():
	string {

		switch($this->Data->Mode) {
			case 'empty':
				return sprintf('<div class="pt-4 pb-4"></div>');
			break;
		}

		return sprintf(
			'<hr />'
		);
	}

}
