<?php

namespace Nether\Common\Struct\EditorJS\Blocks;

use Nether\Common;

class Paragraph
extends Common\Struct\EditorJS\Block {

	protected function
	OnReady(Common\Prototype\ConstructArgs $Argv):
	void {

		parent::OnReady($Argv);

		// the lib i liked was merged into sf so now i need to find another.

		//($this->Data)
		//->Text(Atlantis\Util\Filters::FilteredHTMLCallable());

		return;
	}

	public function
	__ToString():
	string {

		return "<div class=\"mb-4\">{$this->Data->Text}</div>\n";
	}

}
