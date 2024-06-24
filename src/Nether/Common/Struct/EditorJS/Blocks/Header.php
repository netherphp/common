<?php ##########################################################################
################################################################################

namespace Nether\Common\Struct\EditorJS\Blocks;

use Nether\Atlantis;
use Nether\Common;

################################################################################
################################################################################

class Header
extends Common\Struct\EditorJS\Block {

	protected function
	OnReady(Common\Prototype\ConstructArgs $Raw):
	void {

		parent::OnReady($Raw);

		($this->Data)
		->Text(Common\Filters\Text::Trimmed(...))
		->Level(Common\Filters\Numbers::IntType(...));

		return;
	}

	public function
	__ToString():
	string {

		$Level = $this->Data->Level ?: 1;
		$Tag = "h{$Level}";

		return "<{$Tag} class=\"atl-editorjs-header\">{$this->Data->Text}<hr /></{$Tag}>\n";
	}

}
