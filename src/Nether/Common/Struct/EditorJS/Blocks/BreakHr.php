<?php ##########################################################################
################################################################################

namespace Nether\Common\Struct\EditorJS\Blocks;

use Nether\Common;

################################################################################
################################################################################

#[Common\meta\Info('Pairs with editorjs/tools/atl-break.js')]
class BreakHr
extends Common\Struct\EditorJS\Block {

	protected function
	OnReady(Common\Prototype\ConstructArgs $Argv):
	void {
		parent::OnReady($Argv);

		($this->Data)
		->Mode(Common\Filters\Text::Stripped(...));

		return;
	}

	public function
	__ToString():
	string {

		return sprintf(
			'<hr class="atl-editorjs-hr %s" />',
			$this->Data->Mode
		);
	}

}
